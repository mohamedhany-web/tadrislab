<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\ConsultationRequest;
use App\Models\ConsultationService;
use App\Models\ConsultationSetting;
use App\Models\InstructorProfile;
use App\Models\Order;
use App\Models\User;
use App\Models\Wallet;
use App\Services\KashierService;
use App\Services\KashierSettings;
use App\Services\OneToOneAvailabilityService;
use App\Services\PayPalSettings;
use App\Services\PaymentGatewaySettings;
use App\Support\PlatformModules;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * رحلة حجز الاستشارات العامة (مستقلة عن المسارات).
 * choose type → service → slot → data → payment (gateway|bank|package) → confirm (admin).
 */
class ConsultationBookingController extends Controller
{
    public function index(): View|RedirectResponse
    {
        $this->ensureEnabled();

        $types = collect(config('platform.consultations.types', []))
            ->filter(fn ($meta) => ! is_array($meta) || ($meta['mvp'] ?? true) !== false);

        $services = ConsultationService::query()->bookable()->ordered()->get()->groupBy('consultation_type');

        return view('public.consultations.book-index', [
            'types' => $types,
            'servicesByType' => $services,
            'settings' => ConsultationSetting::current(),
        ]);
    }

    public function showService(string $slug): View|RedirectResponse
    {
        $this->ensureEnabled();

        $service = ConsultationService::query()->bookable()->where('slug', $slug)->firstOrFail();
        $instructor = $service->defaultInstructor
            ?? ($service->default_instructor_id ? User::find($service->default_instructor_id) : null);

        $slots = collect();
        if ($instructor && $service->requires_instructor) {
            $slots = OneToOneAvailabilityService::availableSlots(
                (int) $instructor->id,
                now()->addHour(),
                now()->addDays(14),
                (int) $service->duration_minutes
            )->take(24);
        }

        $instructors = User::query()
            ->whereIn('role', ['instructor', 'teacher'])
            ->where('is_active', true)
            ->whereHas('instructorProfile', fn ($q) => $q->approved())
            ->orderBy('name')
            ->limit(80)
            ->get(['id', 'name'])
            ->filter(fn (User $u) => $this->instructorCanDeliverConsultation($u, $service))
            ->values();

        $remainingSessions = Auth::check()
            ? \App\Services\PackageEntitlementService::remainingConsultationSessions(Auth::user())
            : 0;

        return view('public.consultations.book-service', [
            'service' => $service,
            'instructor' => $instructor,
            'slots' => $slots,
            'instructors' => $instructors,
            'remainingSessions' => $remainingSessions,
            'paypalReady' => PayPalSettings::isReady(),
            'kashierReady' => KashierSettings::isReady(),
        ]);
    }

    public function store(Request $request, string $slug): RedirectResponse
    {
        $this->ensureEnabled();

        $service = ConsultationService::query()->bookable()->where('slug', $slug)->firstOrFail();
        $settings = ConsultationSetting::current();
        if (! $settings->is_active) {
            return back()->withErrors(['service' => 'خدمة الاستشارات غير مفعّلة حالياً.']);
        }

        if (! Auth::check()) {
            return redirect()
                ->route('login', ['redirect' => route('public.consultations.book.service', $service->slug)])
                ->with('error', 'سجّل الدخول لإكمال الحجز.');
        }

        $user = Auth::user();
        if ($user->role !== 'student' && ! \App\Support\TadrisRoles::isTeacherLearner($user)) {
            return back()->withErrors(['auth' => 'الحجز متاح لحساب المعلم (متلقّي الخدمة).']);
        }

        $remainingSessions = \App\Services\PackageEntitlementService::remainingConsultationSessions($user);
        $usePackageSession = $remainingSessions > 0 && $request->boolean('use_package_session');
        $amount = (float) $service->price;
        $isFree = $amount < 0.01;

        $onlineMethods = ['online', 'paypal', 'kashier', 'bank_transfer'];
        $manualMethods = ['cash', 'other'];
        $allowedMethods = array_merge($onlineMethods, $manualMethods);

        $availableWallets = $this->platformWalletsQuery()->get();
        $walletRules = ['nullable', 'integer', 'exists:wallets,id'];
        if (! $usePackageSession
            && ! $isFree
            && $availableWallets->isNotEmpty()
            && $request->input('payment_method') === 'bank_transfer') {
            $walletRules = ['required', 'integer', 'exists:wallets,id'];
        }

        $needsGatewayPayment = ! $usePackageSession && ! $isFree;
        $methodInput = (string) $request->input('payment_method', '');
        $proofRequired = $needsGatewayPayment && $methodInput === 'bank_transfer';

        $data = $request->validate([
            'instructor_id' => [$service->requires_instructor ? 'required' : 'nullable', 'exists:users,id'],
            'preferred_slot_at' => ['nullable', 'date'],
            'contact_name' => ['required', 'string', 'max:255'],
            'contact_phone' => ['required', 'string', 'max:40'],
            'contact_email' => ['required', 'email', 'max:255'],
            'organization_name' => [$service->consultation_type === 'institution' ? 'required' : 'nullable', 'string', 'max:255'],
            'student_message' => ['nullable', 'string', 'max:5000'],
            'payment_method' => [$needsGatewayPayment ? 'required' : 'nullable', 'in:'.implode(',', $allowedMethods)],
            'wallet_id' => $walletRules,
            'payment_proof' => [
                $proofRequired ? 'required' : 'nullable',
                'file',
                'mimes:jpeg,png,jpg,pdf',
                'max:'.config('upload_limits.max_upload_kb', 5120),
            ],
            'payment_reference' => ['nullable', 'string', 'max:500'],
            'use_package_session' => ['nullable', 'boolean'],
        ], [
            'instructor_id.required' => 'اختر المستشار/المدرب.',
            'contact_name.required' => 'الاسم مطلوب.',
            'contact_phone.required' => 'رقم واتساب مطلوب لإرسال تفاصيل الموعد.',
            'contact_email.required' => 'البريد مطلوب.',
            'organization_name.required' => 'اسم الجهة مطلوب لاستشارات المؤسسات.',
            'payment_method.required' => 'اختر طريقة الدفع.',
            'payment_proof.required' => 'أرفق إيصال التحويل.',
        ]);

        $instructorId = $data['instructor_id'] ?? $service->default_instructor_id;
        if ($service->requires_instructor && ! $instructorId) {
            return back()->withErrors(['instructor_id' => 'لا يوجد مدرب متاح لهذه الخدمة.'])->withInput();
        }

        if ($instructorId) {
            $instructor = User::findOrFail($instructorId);
            if (! $instructor->isInstructor()) {
                return back()->withErrors(['instructor_id' => 'المدرب غير صالح.'])->withInput();
            }
            InstructorProfile::where('user_id', $instructor->id)->approved()->firstOrFail();
            if (! $this->instructorCanDeliverConsultation($instructor, $service)) {
                return back()->withErrors(['instructor_id' => 'هذا المدرب غير مُسند لتقديم الاستشارات.'])->withInput();
            }
        }

        $preferredSlot = null;
        if (! empty($data['preferred_slot_at']) && $instructorId) {
            $preferredSlot = Carbon::parse($data['preferred_slot_at'])->utc();
            if (! OneToOneAvailabilityService::isSlotAvailable(
                (int) $instructorId,
                $preferredSlot,
                (int) $service->duration_minutes
            )) {
                return back()->withErrors([
                    'preferred_slot_at' => 'الموعد المختار غير متاح. اختر موعدًا من الأوقات الظاهرة أو اترك الحقل فارغًا.',
                ])->withInput();
            }
        }

        $consumed = null;
        if ($usePackageSession) {
            $consumed = \App\Services\PackageEntitlementService::consumeConsultationSessionForUser($user);
            if (! $consumed) {
                return back()->withErrors(['use_package_session' => 'لا يوجد رصيد جلسات استشارة في باقتك.'])->withInput();
            }
        }

        $rawMethod = $usePackageSession || $isFree
            ? 'other'
            : (string) ($data['payment_method'] ?? 'bank_transfer');

        $gatewayHint = null;
        $orderPaymentMethod = $rawMethod;
        if (in_array($rawMethod, ['paypal', 'kashier'], true)) {
            $gatewayHint = $rawMethod;
            $orderPaymentMethod = 'online';
        } elseif ($rawMethod === 'online') {
            $gatewayHint = 'auto';
            $orderPaymentMethod = 'online';
        }

        $paymentProofPath = null;
        if ($needsGatewayPayment && $request->hasFile('payment_proof')) {
            $paymentProofPath = $request->file('payment_proof')->store('payment-proofs', 'public');
        }

        $usesOnlinePipeline = $needsGatewayPayment && in_array($rawMethod, $onlineMethods, true);

        try {
            /** @var array{consultation: ConsultationRequest, order: ?Order} $created */
            $created = DB::transaction(function () use (
                $service,
                $user,
                $instructorId,
                $preferredSlot,
                $data,
                $usePackageSession,
                $consumed,
                $isFree,
                $amount,
                $rawMethod,
                $orderPaymentMethod,
                $paymentProofPath,
                $usesOnlinePipeline
            ) {
                $consultation = ConsultationRequest::create([
                    'consultation_service_id' => $service->id,
                    'consultation_type' => $service->consultation_type,
                    'instructor_id' => $instructorId,
                    'student_id' => $user->id,
                    'price_amount' => ($usePackageSession || $isFree) ? 0 : $amount,
                    'currency' => $service->currency ?: platform_currency(),
                    'duration_minutes' => $service->duration_minutes,
                    'student_message' => $data['student_message'] ?? null,
                    'contact_name' => $data['contact_name'],
                    'contact_phone' => $data['contact_phone'],
                    'contact_email' => $data['contact_email'],
                    'organization_name' => $data['organization_name'] ?? null,
                    'preferred_slot_at' => $preferredSlot,
                    'payment_reference' => $usePackageSession
                        ? ('package_entitlement:'.($consumed?->id ?? ''))
                        : ($data['payment_reference'] ?? null),
                    'status' => ($usePackageSession || $isFree)
                        ? ConsultationRequest::STATUS_PAID
                        : ConsultationRequest::STATUS_NEW,
                    'payment_reported_at' => ($usePackageSession || $isFree || ! $usesOnlinePipeline || $orderPaymentMethod === 'bank_transfer')
                        ? now()
                        : null,
                    'paid_confirmed_at' => ($usePackageSession || $isFree) ? now() : null,
                    'platform_wallet_id' => (! $usePackageSession && $orderPaymentMethod === 'bank_transfer')
                        ? ($data['wallet_id'] ?? null)
                        : null,
                    'payment_method' => $usePackageSession ? 'other' : $rawMethod,
                    'payment_proof' => $paymentProofPath,
                ]);

                $order = null;
                if ($usesOnlinePipeline) {
                    $order = Order::create([
                        'user_id' => $user->id,
                        'order_type' => Order::TYPE_CONSULTATION,
                        'original_amount' => $amount,
                        'amount' => $amount,
                        'currency' => $service->currency ?: platform_currency(),
                        'payment_method' => $orderPaymentMethod,
                        'payment_proof' => $paymentProofPath,
                        'status' => Order::STATUS_PENDING,
                        'notes' => 'حجز استشارة: '.$service->title(),
                        'custom_package_data' => [
                            'consultation_request_id' => $consultation->id,
                            'consultation_service_id' => $service->id,
                            'consultation_service_slug' => $service->slug,
                            'consultation_title' => $service->title(),
                        ],
                    ]);

                    $consultation->update(['order_id' => $order->id]);
                }

                return ['consultation' => $consultation->fresh(), 'order' => $order?->fresh()];
            });
        } catch (\Throwable $e) {
            if ($consumed) {
                \App\Services\PackageEntitlementService::restoreConsultationSessionFromReference(
                    'package_entitlement:'.$consumed->id
                );
            }
            report($e);

            return back()->withErrors(['service' => 'تعذّر إتمام الحجز. حاول مرة أخرى.'])->withInput();
        }

        $consultation = $created['consultation'];
        $order = $created['order'];

        if ($usePackageSession) {
            return redirect()
                ->route('consultations.show', $consultation)
                ->with('success', 'تم خصم جلسة من باقتك وإرسال الحجز. بعد تأكيد الإدارة يصلك الموعد عبر واتساب والبريد.');
        }

        if ($isFree) {
            return redirect()
                ->route('consultations.show', $consultation)
                ->with('success', 'تم تسجيل الحجز المجاني. بعد تأكيد الإدارة يصلك الموعد عبر واتساب والبريد.');
        }

        if (! $usesOnlinePipeline) {
            return redirect()
                ->route('consultations.show', $consultation)
                ->with('success', 'تم إرسال الحجز. بعد مراجعة الدفع ستؤكّد الإدارة الموعد.');
        }

        // Online / bank pipeline via Order
        if ($orderPaymentMethod === 'bank_transfer') {
            return redirect()
                ->route('orders.show', $order)
                ->with('success', 'تم تسجيل الحجز والطلب. سنؤكّد الدفع بعد مراجعة التحويل ثم نحدد الموعد.');
        }

        return $this->redirectToGateway($request, $order, $service, $gatewayHint);
    }

    private function redirectToGateway(
        Request $request,
        Order $order,
        ConsultationService $service,
        ?string $gatewayHint
    ): RedirectResponse {
        if ($gatewayHint === 'paypal' || ($gatewayHint === 'auto' && PayPalSettings::isReady())) {
            return app(PayPalCheckoutController::class)->startExistingOrder($request, $order);
        }

        if ($gatewayHint === 'kashier' || ($gatewayHint === 'auto' && KashierSettings::isReady())) {
            return $this->redirectKashier($order, $service->title());
        }

        if (PaymentGatewaySettings::isFawaterakEnabled()) {
            return redirect()
                ->route('orders.show', $order)
                ->with('info', 'الحجز جاهز — أكمل الدفع من صفحة الطلب أو تواصل مع الدعم.');
        }

        return redirect()
            ->route('orders.show', $order)
            ->with('error', 'لا توجد بوابة دفع جاهزة. يمكنك التحويل البنكي أو التواصل مع الدعم.');
    }

    private function redirectKashier(Order $order, string $title): RedirectResponse
    {
        if (! KashierSettings::isReady()) {
            return redirect()
                ->route('orders.show', $order)
                ->with('error', 'بوابة كاشير غير جاهزة.');
        }

        try {
            $sessionUrl = app(KashierService::class)->getHppUrl(
                (string) $order->id,
                (float) $order->amount,
                url()->route('public.checkout.kashier.callback'),
                KashierSettings::currency(),
                Auth::user()->email,
                (string) Auth::id(),
                $title
            );
        } catch (\Throwable $e) {
            report($e);

            return redirect()
                ->route('orders.show', $order)
                ->with('error', $e->getMessage() ?: 'تعذّر فتح كاشير.');
        }

        return redirect()->away($sessionUrl);
    }

    private function ensureEnabled(): void
    {
        if (class_exists(PlatformModules::class) && method_exists(PlatformModules::class, 'enabled')) {
            abort_unless(PlatformModules::enabled('consultations'), 404);
        }
    }

    private function instructorCanDeliverConsultation(User $instructor, ConsultationService $service): bool
    {
        if ((int) $instructor->id === (int) $service->default_instructor_id) {
            return true;
        }

        if (! $instructor->instructorDeliveryEnabled()) {
            return false;
        }

        if ($instructor->hasGrantedServices()) {
            return $instructor->canDeliverService('consultations');
        }

        return true;
    }

    private function platformWalletsQuery()
    {
        return Wallet::where('is_active', true)
            ->whereNotNull('type')
            ->whereIn('type', ['vodafone_cash', 'instapay', 'bank_transfer'])
            ->where(function ($query) {
                $query->whereNotNull('account_number')
                    ->orWhereNotNull('name');
            })
            ->orderBy('type')
            ->orderBy('name');
    }
}
