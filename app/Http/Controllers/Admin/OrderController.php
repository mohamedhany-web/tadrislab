<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Payment;
use App\Models\SalesOrderNote;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use App\Services\CourseSubscriptionService;
use App\Services\FawaterakPaymentVerifier;
use App\Services\OrderWalletAndCouponFinalizer;
use App\Support\SearchInput;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    /**
     * عرض قائمة الطلبات
     * محمي من: XSS, SQL Injection
     */
    public function index(Request $request)
    {
        $query = Order::with([
            'user',
            'course.academicSubject',
            'course.academicYear',
            'learningPath',
            'salesOwner',
            'servicePackage',
            'tutoringGroup:id,title,type',
        ])->withCount(['serviceEntitlements', 'tutoringGroupBookings']);

        // فلترة حسب مندوب المبيعات
        if ($request->filled('sales_owner_id')) {
            $so = $request->input('sales_owner_id');
            if ($so === 'unassigned') {
                $query->whereNull('sales_owner_id');
            } elseif (ctype_digit((string) $so)) {
                $query->where('sales_owner_id', (int) $so);
            }
        }

        // فلترة حسب الحالة - حماية من SQL Injection
        if ($request->filled('status')) {
            $status = strip_tags(trim($request->status));
            if (in_array($status, ['pending', 'approved', 'rejected'])) {
                $query->where('status', $status);
            }
        }

        // فلترة حسب طريقة الدفع - حماية من SQL Injection
        if ($request->filled('payment_method')) {
            $paymentMethod = strip_tags(trim($request->payment_method));
            if (in_array($paymentMethod, ['bank_transfer', 'cash', 'online', 'other'])) {
                $query->where('payment_method', $paymentMethod);
            }
        }

        // البحث - حماية من XSS و SQL Injection
        if ($request->filled('search')) {
            $search = SearchInput::sanitizeForLike((string) $request->search);
            if ($search !== '') {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    })->orWhereHas('course', function ($cq) use ($search) {
                        $cq->where('title', 'like', "%{$search}%");
                    })->orWhereHas('learningPath', function ($lq) use ($search) {
                        $lq->where('name', 'like', "%{$search}%");
                    })->orWhereHas('servicePackage', function ($packageQuery) use ($search) {
                        $packageQuery->where('name', 'like', "%{$search}%");
                    })->orWhereHas('tutoringGroup', function ($groupQuery) use ($search) {
                        $groupQuery->where('title', 'like', "%{$search}%");
                    })->orWhere('custom_package_data', 'like', "%{$search}%");
                });
            }
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(20);

        // إحصائيات سريعة
        $stats = [
            'total' => Order::count(),
            'pending' => Order::pending()->count(),
            'approved' => Order::approved()->count(),
            'rejected' => Order::rejected()->count(),
        ];

        $salesEmployees = User::query()
            ->where('is_employee', true)
            ->whereHas('employeeJob', fn ($q) => $q->where('code', 'sales'))
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('admin.orders.index', compact('orders', 'stats', 'salesEmployees'));
    }

    /**
     * عرض تفاصيل الطلب
     * محمي من: Unauthorized Access
     */
    public function show(Order $order)
    {
        $order->load([
            'user',
            'course.academicSubject',
            'course.academicYear',
            'learningPath',
            'approver',
            'wallet',
            'salesOwner',
            'salesNotes.user',
            'servicePackage',
            'tutoringGroup.instructor:id,name',
            'serviceEntitlements.tutoringGroup:id,title',
            'serviceEntitlements.bookings:id,student_service_entitlement_id,status,starts_at,instructor_id',
            'tutoringGroupBookings.instructor:id,name',
            'tutoringGroupBookings.tutoringGroup:id,title',
            'tutoringGroupBookings.classroomMeeting:id,code',
        ]);

        $platformWallets = Wallet::where('is_active', true)
            ->whereIn('type', ['vodafone_cash', 'instapay', 'bank_transfer'])
            ->orderBy('type')
            ->orderBy('name')
            ->get();

        $salesEmployees = User::query()
            ->where('is_employee', true)
            ->whereHas('employeeJob', fn ($q) => $q->where('code', 'sales'))
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        return view('admin.orders.show', compact('order', 'platformWallets', 'salesEmployees'));
    }

    /**
     * إعادة محاولة تفعيل رصيد/حجز التدريس بعد فشل fulfill (مثلاً بعد دفع أونلاين).
     */
    public function refulfillTutoring(Order $order)
    {
        if ($order->status !== Order::STATUS_APPROVED) {
            return back()->with('error', 'إعادة التفعيل متاحة للطلبات المعتمدة فقط.');
        }
        if (! $order->isTutoringOrder()) {
            return back()->with('error', 'هذا الطلب ليس طلب تدريس/باقة حصص.');
        }

        try {
            \App\Services\TutoringGroupCheckoutService::fulfillApprovedOrder($order->fresh());

            $notes = (string) ($order->notes ?? '');
            if (str_contains($notes, '[TUTORING_FULFILL_FAILED]')) {
                $cleaned = trim(preg_replace('/^\[TUTORING_FULFILL_FAILED\].*$/m', '', $notes) ?? '');
                $order->update(['notes' => $cleaned !== '' ? $cleaned : null]);
            }

            return back()->with('success', 'تمت إعادة تفعيل رصيد الباقة بنجاح.');
        } catch (\Throwable $e) {
            Log::error('Admin tutoring refulfill failed: '.$e->getMessage(), ['order_id' => $order->id]);

            return back()->with('error', 'تعذر التفعيل: '.$e->getMessage());
        }
    }

    /**
     * تأكيد دفع فواتيرك يدوياً لطلب معلّق (عند نجاح الدفع دون تفعيل تلقائي).
     */
    public function reconcileFawaterak(Request $request, Order $order)
    {
        if ($order->status !== Order::STATUS_PENDING) {
            return back()->with('error', 'يمكن تأكيد فواتيرك للطلبات المعلّقة فقط.');
        }
        if ($order->payment_method !== 'online') {
            return back()->with('error', 'هذا الطلب ليس دفعاً إلكترونياً.');
        }

        $data = $request->validate([
            'fawaterak_invoice_id' => ['nullable', 'string', 'max:64'],
        ]);

        $invoiceId = trim((string) ($data['fawaterak_invoice_id'] ?? $order->fawaterak_invoice_id ?? ''));
        if ($invoiceId === '') {
            return back()->with('error', 'أدخل رقم فاتورة فواتيرك أو تأكد أنه محفوظ على الطلب.');
        }

        try {
            DB::transaction(function () use ($order, $invoiceId) {
                $locked = Order::query()->whereKey($order->id)->lockForUpdate()->first();
                if (! $locked || $locked->status !== Order::STATUS_PENDING) {
                    return;
                }

                app(FawaterakPaymentVerifier::class)->assertOrderPaid($locked, null, [
                    'invoice_id' => $invoiceId,
                ]);

                app(\App\Http\Controllers\Public\CheckoutController::class)
                    ->approveOrderAfterOnlinePaymentPublic(
                        $locked,
                        'fawaterak',
                        $invoiceId,
                        ['source' => 'admin_reconcile', 'invoice_id' => $invoiceId],
                        'فواتيرك (تأكيد يدوي)'
                    );
            });

            return back()->with('success', 'تم تأكيد الدفع وتفعيل الطلب والباقة وإنشاء الفاتورة والمعاملة.');
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            Log::error('Admin Fawaterak reconcile failed', [
                'order_id' => $order->id,
                'message' => $e->getMessage(),
            ]);

            return back()->with('error', 'تعذر التأكيد: '.$e->getMessage());
        }
    }

    /**
     * تحديث محفظة الاستلام على المنصة (لطلبات التحويل المعلقة) حتى يُسجَّل الإيداع عند الموافقة
     */
    public function updateReceivingWallet(Request $request, Order $order)
    {
        if ($order->status !== Order::STATUS_PENDING) {
            return back()->with('error', 'يمكن تعديل حساب الاستلام للطلبات قيد الانتظار فقط.');
        }

        if (! in_array($order->payment_method, ['bank_transfer', 'wallet'], true)) {
            return back()->with('error', 'تعديل المحفظة متاح فقط لطلبات التحويل البنكي أو المحفظة الإلكترونية.');
        }

        $validated = $request->validate([
            'wallet_id' => [
                'required',
                Rule::exists('wallets', 'id')->where('is_active', true)->whereIn('type', ['vodafone_cash', 'instapay', 'bank_transfer']),
            ],
        ], [
            'wallet_id.required' => 'اختر حساب الاستلام على المنصة.',
            'wallet_id.exists' => 'الحساب غير صالح أو غير مفعّل.',
        ]);

        $order->update(['wallet_id' => $validated['wallet_id']]);

        return back()->with('success', 'تم حفظ حساب الاستلام. عند الموافقة سيُسجَّل المبلغ على هذه المحفظة وفي سجل المعاملات.');
    }

    /**
     * الموافقة على الطلب
     * محمي من: XSS, SQL Injection, CSRF, Brute Force, Race Conditions
     */
    public function approve(Request $request, Order $order)
    {
        $isAjax = $request->wantsJson() || $request->ajax();
        Log::info('Order approve: start', ['order_id' => $order->id, 'status' => $order->status, 'academic_year_id' => $order->academic_year_id, 'advanced_course_id' => $order->advanced_course_id]);

        try {
            // Rate Limiting - حماية من Brute Force
            $key = 'order_approve_'.Auth::id();
            $maxAttempts = 10;
            $decayMinutes = 1;

            if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
                $seconds = RateLimiter::availableIn($key);
                $msg = "تم تجاوز عدد المحاولات المسموح. يرجى المحاولة بعد {$seconds} ثانية.";
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json(['success' => false, 'error' => $msg], 429);
                }

                return back()->with('error', $msg);
            }

            RateLimiter::hit($key, $decayMinutes * 60);

            // التحقق من حالة الطلب
            if ($order->status !== Order::STATUS_PENDING) {
                RateLimiter::clear($key);
                $msg = 'لا يمكن الموافقة على هذا الطلب';
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json(['success' => false, 'error' => $msg], 400);
                }

                return back()->with('error', $msg);
            }

            try {
                DB::beginTransaction();

                // إعادة تحميل الطلب لتجنب Race Conditions
                $order->refresh();

                // التحقق مرة أخرى بعد إعادة التحميل
                if ($order->status !== Order::STATUS_PENDING) {
                    DB::rollBack();
                    RateLimiter::clear($key);
                    $msg = 'تم تعديل حالة الطلب بالفعل';
                    if ($request->wantsJson() || $request->ajax()) {
                        return response()->json(['success' => false, 'error' => $msg], 409);
                    }

                    return back()->with('error', $msg);
                }

                // التحقق من عدم وجود فاتورة للطلب مسبقاً
                if ($order->invoice_id) {
                    DB::rollBack();
                    RateLimiter::clear($key);
                    $msg = 'تم إنشاء فاتورة لهذا الطلب مسبقاً';
                    if ($request->wantsJson() || $request->ajax()) {
                        return response()->json(['success' => false, 'error' => $msg], 409);
                    }

                    return back()->with('error', $msg);
                }

                // التحقق من وجود المستخدم المرتبط بالطلب (تجنب foreign key violation)
                $orderUser = \App\Models\User::find($order->user_id);
                if (! $orderUser) {
                    DB::rollBack();
                    RateLimiter::clear($key);
                    $msg = 'المستخدم المرتبط بالطلب غير موجود في النظام. يرجى التحقق من بيانات الطلب.';
                    Log::warning('Order approve: order user not found', ['order_id' => $order->id, 'user_id' => $order->user_id]);
                    if ($request->wantsJson() || $request->ajax()) {
                        return response()->json(['success' => false, 'error' => $msg], 400);
                    }

                    return back()->with('error', $msg);
                }

                $requiresReceivingWallet = in_array($order->payment_method, ['bank_transfer', 'wallet'], true);
                if ($requiresReceivingWallet && ! $order->wallet_id) {
                    DB::rollBack();
                    RateLimiter::clear($key);
                    $msg = 'لا يمكن الموافقة قبل تحديد حساب الاستلام على المنصة. استخدم نموذج «حساب التحويل على المنصة» في صفحة الطلب ثم أعد الموافقة.';
                    if ($request->wantsJson() || $request->ajax()) {
                        return response()->json(['success' => false, 'error' => $msg], 422);
                    }

                    return back()->with('error', $msg);
                }

                $order->loadMissing(['course', 'learningPath', 'servicePackage']);

                if ($order->order_type === \App\Models\Order::TYPE_CUSTOM_SERVICE_PACKAGE) {
                    $orderTitle = htmlspecialchars($order->custom_package_data['name'] ?? 'باقة مخصصة', ENT_QUOTES, 'UTF-8');
                } elseif ($order->servicePackage) {
                    $orderTitle = htmlspecialchars($order->servicePackage->name, ENT_QUOTES, 'UTF-8');
                } elseif ($order->course) {
                    $orderTitle = htmlspecialchars($order->course->title ?? 'كورس', ENT_QUOTES, 'UTF-8');
                } elseif ($order->academic_year_id) {
                    if (! $order->relationLoaded('learningPath')) {
                        $order->load('learningPath');
                    }
                    $orderTitle = $order->learningPath
                        ? htmlspecialchars($order->learningPath->name ?? 'طلب قديم', ENT_QUOTES, 'UTF-8')
                        : 'طلب قديم';
                } else {
                    $orderTitle = 'غير محدد';
                }
                $orderType = 'course';

                // إنشاء الفاتورة تلقائياً
                Log::info('Order approve: creating invoice', ['order_id' => $order->id, 'type' => $orderType]);
                $invoiceNumber = 'INV-'.str_pad(Invoice::count() + 1, 8, '0', STR_PAD_LEFT);
                $invOrig = (float) ($order->original_amount ?? $order->amount);
                $invCouponDisc = (float) ($order->discount_amount ?? 0);
                $invWalletDisc = (float) ($order->wallet_credit_amount ?? 0);
                $invDiscountTotal = round($invCouponDisc + $invWalletDisc, 2);
                $invoice = Invoice::create([
                    'invoice_number' => $invoiceNumber,
                    'user_id' => $order->user_id,
                    'type' => $orderType,
                    'description' => $order->advanced_course_id
                        ? 'فاتورة تسجيل في الكورس: '.$orderTitle
                        : 'فاتورة طلب: '.$orderTitle,
                    'subtotal' => $invOrig,
                    'tax_amount' => 0,
                    'discount_amount' => $invDiscountTotal,
                    'total_amount' => $order->amount,
                    'status' => 'paid', // تم الدفع لأنه تم قبول الطلب
                    'due_date' => now(),
                    'paid_at' => now(),
                    'notes' => 'فاتورة مسبقة الدفع - من طلب رقم: '.$order->id,
                    'items' => [
                        [
                            'description' => $order->advanced_course_id
                                ? 'تسجيل في الكورس: '.$orderTitle
                                : 'شراء: '.$orderTitle,
                            'quantity' => 1,
                            'price' => $invOrig,
                            'total' => $invOrig,
                        ],
                    ],
                ]);
                Log::info('Order approve: invoice created', ['order_id' => $order->id, 'invoice_id' => $invoice->id]);

                // إنشاء المدفوعات تلقائياً
                Log::info('Order approve: creating payment', ['order_id' => $order->id]);
                $paymentNumber = Payment::generateUniquePaymentNumber();

                // تحويل طريقة الدفع من order إلى payment
                $paymentMethodMap = [
                    'bank_transfer' => 'bank_transfer',
                    'cash' => 'cash',
                    'online' => 'online',
                    'wallet' => 'wallet',
                    'other' => 'other',
                ];
                $paymentMethod = $paymentMethodMap[$order->payment_method] ?? 'other';

                try {
                    $payment = Payment::create([
                        'payment_number' => $paymentNumber,
                        'invoice_id' => $invoice->id,
                        'user_id' => $order->user_id,
                        'payment_method' => $paymentMethod,
                        'amount' => $order->amount,
                        'currency' => $order->currencyCode(),
                        'status' => 'completed',
                        'paid_at' => now(),
                        'processed_by' => auth()->id(),
                        'notes' => 'دفعة من طلب رقم: '.$order->id.($order->wallet_id ? ' - محفظة: '.$order->wallet_id : ''),
                    ]);
                } catch (\Throwable $e) {
                    Log::error('Order approve: Payment::create FAILED', ['order_id' => $order->id, 'message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
                    throw $e;
                }
                Log::info('Order approve: payment created', ['order_id' => $order->id, 'payment_id' => $payment->id]);

                // ربط المدفوعات بالمحفظة إذا كانت موجودة وإضافة المبلغ للمحفظة
                $wallet = null;
                if ($order->wallet_id) {
                    $payment->update([
                        'wallet_id' => $order->wallet_id,
                    ]);

                    // إضافة المبلغ للمحفظة (إيداع)
                    $wallet = \App\Models\Wallet::find($order->wallet_id);
                    if ($wallet) {
                        $description = 'إيداع من طلب رقم: '.$order->id.' - فاتورة: '.$invoice->invoice_number;
                        $description .= ' - الكورس: '.($order->course?->title ?? ($orderTitle ?: 'طلب قديم'));
                        try {
                            $wallet->deposit(
                                $order->amount,
                                $payment->id,
                                null,
                                $description
                            );
                        } catch (\Throwable $e) {
                            Log::error('Wallet deposit failed during order approval', [
                                'order_id' => $order->id,
                                'wallet_id' => $order->wallet_id,
                                'message' => $e->getMessage(),
                                'trace' => $e->getTraceAsString(),
                            ]);
                            throw $e;
                        }
                    }
                }

                // إنشاء معاملة مالية (إيراد)
                Log::info('Order approve: creating transaction', ['order_id' => $order->id]);
                $transactionDescription = $order->advanced_course_id
                    ? 'دفعة مقابل تسجيل في الكورس: '.($order->course?->title ?? 'كورس')
                    : 'دفعة مقابل طلب قديم: '.$orderTitle;
                $transactionDescription .= ' - طلب رقم: '.$order->id.' - فاتورة: '.$invoice->invoice_number.($wallet ? ' - محفظة: '.(optional($wallet)->name ?? $wallet->id) : '');

                $transaction = Transaction::create([
                    'transaction_number' => Transaction::generateUniqueTransactionNumber(),
                    'user_id' => $order->user_id,
                    'payment_id' => $payment->id,
                    'invoice_id' => $invoice->id,
                    'expense_id' => null,
                    'subscription_id' => null,
                    'type' => 'credit', // دائن (إيراد)
                    'category' => 'course_payment', // المسار والكورس يستخدمان نفس التصنيف (الجدول لا يدعم learning_path_payment)
                    'amount' => $order->amount,
                    'currency' => $order->currencyCode(),
                    'description' => $transactionDescription,
                    'status' => 'completed',
                    'metadata' => [
                        'order_id' => $order->id,
                        'invoice_id' => $invoice->id,
                        'payment_id' => $payment->id,
                        'course_id' => $order->advanced_course_id,
                        'academic_year_id' => $order->academic_year_id,
                        'wallet_id' => $order->wallet_id,
                    ],
                    'created_by' => auth()->id(),
                ]);
                Log::info('Order approve: transaction created', ['order_id' => $order->id, 'transaction_id' => $transaction->id ?? null]);

                // ربط معاملة المحفظة بالمعاملة المالية إذا كانت موجودة
                if ($wallet) {
                    $walletTransaction = \App\Models\WalletTransaction::where('wallet_id', $wallet->id)
                        ->where('payment_id', $payment->id)
                        ->where('type', 'deposit')
                        ->latest()
                        ->first();

                    if ($walletTransaction) {
                        $walletTransaction->update(['transaction_id' => $transaction->id]);
                    }
                }

                // تحديث حالة الطلب وربطه بالفاتورة والمدفوعات
                Log::info('Order approve: updating order status', ['order_id' => $order->id]);
                $order->update([
                    'status' => Order::STATUS_APPROVED,
                    'approved_at' => now(),
                    'approved_by' => auth()->id(),
                    'invoice_id' => $invoice->id,
                    'payment_id' => $payment->id,
                ]);
                Log::info('Order approve: order updated', ['order_id' => $order->id]);

                // تحديث حالة الإحالة إذا كانت موجودة (لا نوقف الموافقة إذا فشل)
                Log::info('Order approve: after order updated', ['order_id' => $order->id]);
                try {
                    app(\App\Services\ReferralService::class)
                        ->completePendingForUser((int) $order->user_id, $order->amount);
                } catch (\Throwable $e) {
                    Log::warning('Referral update failed during order approval: '.$e->getMessage(), ['order_id' => $order->id]);
                }

                try {
                    app(\App\Services\CouponCommissionService::class)->accrueFromApprovedOrder(
                        $order->fresh(),
                        $invoice->id ?? null,
                        $payment->id ?? null
                    );
                } catch (\Throwable $e) {
                    Log::warning('Coupon commission accrual failed during order approval: '.$e->getMessage(), ['order_id' => $order->id]);
                }

                try {
                    \App\Services\Crm\CrmCommissionService::handleOrderApproved($order->fresh(), auth()->user());
                } catch (\Throwable $e) {
                    Log::warning('CRM commission/activation failed during order approval: '.$e->getMessage(), ['order_id' => $order->id]);
                }

                // إذا كان الطلب للكورس، تسجيل الطالب في الكورس (لا نوقف الموافقة إذا فشل)
                if ($order->advanced_course_id) {
                    Log::info('Order approve: starting course enrollment', ['order_id' => $order->id, 'advanced_course_id' => $order->advanced_course_id]);
                    try {
                        if (empty($order->billing_mode)) {
                            $order->loadMissing('course');
                            $order->update([
                                'billing_mode' => $order->course && $order->course->isMonthlyBilling()
                                    ? CourseSubscriptionService::BILLING_MONTHLY
                                    : CourseSubscriptionService::BILLING_ONE_TIME,
                            ]);
                            $order = $order->fresh();
                        }

                        CourseSubscriptionService::syncEnrollmentFromOrder(
                            $order,
                            $invoice->id,
                            $payment->id,
                            $paymentMethod,
                            (int) auth()->id()
                        );
                    } catch (\Throwable $e) {
                        Log::warning('Course enrollment failed during order approval: '.$e->getMessage(), [
                            'order_id' => $order->id,
                            'advanced_course_id' => $order->advanced_course_id,
                            'trace' => $e->getTraceAsString(),
                        ]);
                    }
                    Log::info('Order approve: course enrollment block done', ['order_id' => $order->id]);
                }

                if ($order->isTutoringOrder()) {
                    try {
                        \App\Services\TutoringGroupCheckoutService::fulfillApprovedOrder($order->fresh());
                    } catch (\Throwable $e) {
                        Log::error('Tutoring fulfill failed during order approval: '.$e->getMessage(), ['order_id' => $order->id]);
                        throw new \RuntimeException('تعذر تفعيل رصيد الباقة أو إنشاء الحجز: '.$e->getMessage(), 0, $e);
                    }
                }

                OrderWalletAndCouponFinalizer::run($order->fresh());

                if (\App\Services\CatalogOrderFulfillmentService::isCatalogOrder($order)) {
                    try {
                        \App\Services\CatalogOrderFulfillmentService::fulfill($order->fresh(), auth()->user());
                    } catch (\Throwable $e) {
                        Log::error('Catalog fulfill failed during order approval: '.$e->getMessage(), ['order_id' => $order->id]);
                    }
                }

                if (\App\Services\ConsultationOrderFulfillmentService::isConsultationOrder($order)) {
                    try {
                        \App\Services\ConsultationOrderFulfillmentService::fulfill($order->fresh(), auth()->user());
                    } catch (\Throwable $e) {
                        Log::error('Consultation fulfill failed during order approval: '.$e->getMessage(), ['order_id' => $order->id]);
                    }
                }

                // الـ commit أولاً حتى لا يعطل سجل النشاط استجابة الموافقة
                Log::info('Order approve: before DB::commit', ['order_id' => $order->id]);
                DB::commit();
                RateLimiter::clear($key);

                // تسجيل النشاط بعد الـ commit (إدراج مباشر لتجنب توقف النموذج أو استهلاك الذاكرة)
                try {
                    $logData = [
                        'user_id' => Auth::id(),
                        'action' => 'order_approved',
                        'model_type' => 'Order',
                        'model_id' => $order->id,
                        'new_values' => json_encode([
                            'id' => $order->id,
                            'user_id' => $order->user_id,
                            'status' => $order->status,
                            'approved_at' => $order->approved_at ? $order->approved_at->format('Y-m-d H:i:s') : null,
                            'approved_by' => $order->approved_by,
                            'invoice_id' => $order->invoice_id,
                            'payment_id' => $order->payment_id,
                            'amount' => (float) $order->amount,
                        ]),
                        'ip_address' => $request->ip(),
                        'user_agent' => substr((string) $request->userAgent(), 0, 255),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                    if (\Illuminate\Support\Facades\Schema::hasColumn('activity_logs', 'description')) {
                        $logData['description'] = 'موافقة على طلب #'.$order->id;
                    }
                    DB::table('activity_logs')->insert($logData);
                } catch (\Throwable $e) {
                    Log::warning('Order approve: activity log insert failed (after commit)', ['message' => $e->getMessage()]);
                }

                Log::info('Order approved successfully', [
                    'order_id' => $order->id,
                    'user_id' => $order->user_id,
                    'is_legacy_path_order' => $order->academic_year_id && ! $order->advanced_course_id,
                    'invoice_number' => $invoice->invoice_number ?? null,
                    'payment_number' => $payment->payment_number ?? null,
                ]);

                try {
                    $fresh = $order->fresh(['user']);
                    event(new \App\Events\PaymentSuccessful($fresh, $payment ?? null));
                    event(new \App\Events\OrderStatusChanged($fresh, Order::STATUS_PENDING, Order::STATUS_APPROVED));
                    if ($fresh?->user && $order->advanced_course_id) {
                        event(new \App\Events\AccessSubscriptionActivated(
                            $fresh->user,
                            (string) ($order->course?->title ?? 'كورس / مسار'),
                            'تم التفعيل بعد موافقة الإدارة',
                            $fresh->id
                        ));
                    }
                } catch (\Throwable $e) {
                    Log::warning('Order approve notification dispatch failed', [
                        'order_id' => $order->id,
                        'error' => $e->getMessage(),
                    ]);
                }

                if (in_array($order->order_type, [Order::TYPE_SERVICE_PACKAGE, Order::TYPE_CUSTOM_SERVICE_PACKAGE], true)) {
                    $successMessage = 'تمت الموافقة وتفعيل رصيد حصص الطالب وربطه بالطلب. الفاتورة: '.$invoice->invoice_number.'، والمدفوعات: '.$payment->payment_number;
                } elseif ($order->isTutoringOrder()) {
                    $successMessage = 'تمت الموافقة وتفعيل الاشتراك والحجز المرتبط. الفاتورة: '.$invoice->invoice_number.'، والمدفوعات: '.$payment->payment_number;
                } elseif ($order->advanced_course_id) {
                    $successMessage = 'تمت الموافقة على الطلب وتم تفعيل الكورس للطالب. تم إنشاء الفاتورة رقم: '.$invoice->invoice_number.' والمدفوعات رقم: '.$payment->payment_number;
                } else {
                    $successMessage = 'تمت الموافقة على الطلب. تم إنشاء الفاتورة رقم: '.$invoice->invoice_number.' والمدفوعات رقم: '.$payment->payment_number;
                }

                // إذا كان الطلب AJAX، إرجاع JSON
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => $successMessage,
                        'redirect' => route('admin.orders.show', $order),
                    ]);
                }

                return back()->with('success', $successMessage);

            } catch (\Throwable $e) {
                Log::error('Order approve: CAUGHT', ['msg' => $e->getMessage(), 'class' => get_class($e)]);
                DB::rollBack();
                RateLimiter::clear($key);

                Log::error('Error approving order: '.$e->getMessage(), [
                    'order_id' => $order->id ?? null,
                    'user_id' => Auth::id(),
                    'ip' => $request->ip(),
                    'order_status' => $order->status ?? 'unknown',
                    'order_academic_year_id' => $order->academic_year_id ?? null,
                    'order_advanced_course_id' => $order->advanced_course_id ?? null,
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString(),
                ]);

                $errorMsg = $e->getMessage() ?: 'حدث خطأ غير متوقع';
                Log::error('Order approve: ERROR during processing (سيظهر في الـ logs)', [
                    'order_id' => $order->id ?? null,
                    'message' => $e->getMessage(),
                    'exception' => get_class($e),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString(),
                ]);
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'error' => $errorMsg,
                        'message' => $errorMsg,
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                    ], 500);
                }

                return back()->with('error', 'حدث خطأ أثناء معالجة الطلب: '.$errorMsg);
            }

        } catch (\Throwable $outer) {
            Log::error('Order approve: UNEXPECTED ERROR (سيظهر في الـ logs)', [
                'order_id' => $order->id ?? null,
                'message' => $outer->getMessage(),
                'exception' => get_class($outer),
                'file' => $outer->getFile(),
                'line' => $outer->getLine(),
                'trace' => $outer->getTraceAsString(),
            ]);
            if ($isAjax) {
                return response()->json([
                    'success' => false,
                    'error' => $outer->getMessage() ?: 'حدث خطأ أثناء المعالجة',
                    'message' => $outer->getMessage() ?: 'حدث خطأ أثناء المعالجة',
                    'file' => $outer->getFile(),
                    'line' => $outer->getLine(),
                ], 500);
            }
            throw $outer;
        }
    }

    /**
     * رفض الطلب
     * محمي من: XSS, SQL Injection, CSRF, Brute Force, Race Conditions
     */
    public function reject(Request $request, Order $order)
    {
        $isAjax = $request->wantsJson() || $request->ajax();

        // Rate Limiting - حماية من Brute Force
        $key = 'order_reject_'.Auth::id();
        $maxAttempts = 10;
        $decayMinutes = 1;

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($key);
            $msg = "تم تجاوز عدد المحاولات المسموح. يرجى المحاولة بعد {$seconds} ثانية.";
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'error' => $msg], 429);
            }

            return back()->with('error', $msg);
        }

        RateLimiter::hit($key, $decayMinutes * 60);

        if ($order->status !== Order::STATUS_PENDING) {
            RateLimiter::clear($key);
            $msg = 'لا يمكن رفض هذا الطلب';
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'error' => $msg], 400);
            }

            return back()->with('error', $msg);
        }

        try {
            DB::beginTransaction();

            $order->refresh();

            if ($order->status !== Order::STATUS_PENDING) {
                DB::rollBack();
                RateLimiter::clear($key);
                $msg = 'تم تعديل حالة الطلب بالفعل';
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json(['success' => false, 'error' => $msg], 409);
                }

                return back()->with('error', $msg);
            }

            $rejectionReason = strip_tags(trim((string) $request->input('rejection_reason', '')));
            $updateData = [
                'status' => Order::STATUS_REJECTED,
                'approved_by' => Auth::id(),
            ];
            if ($rejectionReason !== '') {
                $updateData['notes'] = trim(($order->notes ? $order->notes."\n" : '').'سبب الرفض: '.$rejectionReason);
            }
            $order->update($updateData);

            DB::commit();
            RateLimiter::clear($key);

            try {
                event(new \App\Events\OrderStatusChanged(
                    $order->fresh(['user']),
                    Order::STATUS_PENDING,
                    Order::STATUS_REJECTED,
                    $rejectionReason !== '' ? 'سبب الرفض: '.$rejectionReason : ''
                ));
            } catch (\Throwable $e) {
                Log::warning('Order reject notification dispatch failed', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage(),
                ]);
            }

            // تسجيل النشاط بعد الـ commit (إدراج مباشر كما في الموافقة)
            try {
                $logData = [
                    'user_id' => Auth::id(),
                    'action' => 'order_rejected',
                    'model_type' => 'Order',
                    'model_id' => $order->id,
                    'new_values' => json_encode([
                        'id' => $order->id,
                        'user_id' => $order->user_id,
                        'status' => $order->status,
                        'approved_by' => $order->approved_by,
                    ]),
                    'ip_address' => $request->ip(),
                    'user_agent' => substr((string) $request->userAgent(), 0, 255),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                if (\Illuminate\Support\Facades\Schema::hasColumn('activity_logs', 'description')) {
                    $logData['description'] = 'رفض طلب #'.$order->id;
                }
                DB::table('activity_logs')->insert($logData);
            } catch (\Throwable $logEx) {
                Log::warning('Order reject: activity log insert failed', ['message' => $logEx->getMessage()]);
            }

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'تم رفض الطلب بنجاح',
                    'redirect' => route('admin.orders.show', $order),
                ]);
            }

            return back()->with('success', 'تم رفض الطلب بنجاح');

        } catch (\Throwable $e) {
            DB::rollBack();
            RateLimiter::clear($key);

            Log::error('Error rejecting order: '.$e->getMessage(), [
                'order_id' => $order->id ?? null,
                'user_id' => Auth::id(),
                'ip' => $request->ip(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            $errorMsg = $e->getMessage() ?: 'حدث خطأ غير متوقع';
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'error' => $errorMsg, 'message' => $errorMsg], 500);
            }

            return back()->with('error', 'حدث خطأ أثناء معالجة الطلب. يرجى المحاولة مرة أخرى.');
        }
    }

    public function assignSalesOwner(Request $request, Order $order)
    {
        $validated = $request->validate([
            'sales_owner_id' => 'nullable|exists:users,id',
        ]);

        if (! empty($validated['sales_owner_id'])) {
            $salesUser = User::find($validated['sales_owner_id']);
            if (! $salesUser || ! $salesUser->is_employee) {
                return back()->with('error', 'يجب اختيار موظف نشط.');
            }
        }

        $order->update(['sales_owner_id' => $validated['sales_owner_id'] ?? null]);

        return back()->with('success', 'تم تحديث مندوب المبيعات على الطلب.');
    }

    public function storeSalesNote(Request $request, Order $order)
    {
        $validated = $request->validate([
            'body' => 'required|string|max:5000',
        ]);

        SalesOrderNote::create([
            'order_id' => $order->id,
            'user_id' => Auth::id(),
            'body' => $validated['body'],
        ]);

        $order->update(['sales_contacted_at' => now()]);

        return back()->with('success', 'تمت إضافة ملاحظة المبيعات.');
    }
}
