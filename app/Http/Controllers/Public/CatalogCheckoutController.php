<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\LearningPath;
use App\Models\Order;
use App\Models\Package;
use App\Services\CatalogOrderFulfillmentService;
use App\Services\KashierService;
use App\Services\KashierSettings;
use App\Services\PayPalSettings;
use App\Services\PaymentGatewaySettings;
use App\Services\PlatformPaymentAccountService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Brief journeys: Buy Package / Buy Learning Path → Checkout → Payment → Entitlement.
 */
class CatalogCheckoutController extends Controller
{
    public function showPackage(string $slug): View
    {
        $package = Package::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->with([
                'learningPaths' => fn ($q) => $q->where('is_active', true),
                'courses' => fn ($q) => $q->where('is_active', true),
            ])
            ->firstOrFail();

        $related = Package::query()
            ->where('is_active', true)
            ->where('id', '!=', $package->id)
            ->limit(3)
            ->get();

        return view('public.packages.show', [
            'package' => $package,
            'relatedPackages' => $related,
            'laslesNavActive' => 'pricing',
            'pageTitle' => $package->name.' — '.__('common.app_name'),
            'bodyClass' => 'lasles-pricing-page',
        ]);
    }

    public function checkoutPackage(string $slug): View|RedirectResponse
    {
        $package = Package::query()->where('slug', $slug)->where('is_active', true)->firstOrFail();
        if ($package->isQuoteOnly()) {
            return redirect()->route('public.contact', ['topic' => 'package', 'package' => $package->slug]);
        }
        if (! Auth::check()) {
            return redirect()->guest(route('login'))->with('info', 'سجّل الدخول لإتمام شراء الباقة.');
        }

        $features = array_values(array_filter(is_array($package->features) ? $package->features : []));
        $tools = array_values(array_filter(is_array($package->tools_resources) ? $package->tools_resources : []));

        return view('public.catalog-checkout', $this->checkoutViewData(
            kind: 'package',
            title: $package->name,
            priceLabel: $package->formattedPrice(2),
            amount: (float) $package->price,
            currency: $package->currencyCode(),
            isFree: $package->isFreePackage(),
            formAction: route('public.packages.checkout.store', $package->slug),
            backUrl: route('public.package.show', $package->slug),
            subtitle: filled($package->card_summary) ? (string) $package->card_summary : $package->typeLabel(),
            durationLabel: $package->durationLabel(),
            benefits: array_values(array_unique(array_merge($features, $tools))),
        ));
    }

    public function storePackage(Request $request, string $slug): RedirectResponse
    {
        $package = Package::query()->where('slug', $slug)->where('is_active', true)->firstOrFail();
        abort_if($package->isQuoteOnly(), 404);

        return $this->storeCatalogOrder($request, [
            'order_type' => Order::TYPE_PACKAGE,
            'package_id' => $package->id,
            'learning_path_id' => null,
            'amount' => (float) $package->price,
            'currency' => $package->currencyCode(),
            'title' => $package->name,
            'is_free' => $package->isFreePackage(),
            'success_route' => 'student.learning-paths.index',
            'retry_route' => ['public.packages.checkout', $package->slug],
        ]);
    }

    public function checkoutPath(string $slug): View|RedirectResponse
    {
        $path = LearningPath::query()->published()->where('slug', $slug)->firstOrFail();
        abort_unless($path->isStandaloneProduct(), 404);
        if (! Auth::check()) {
            return redirect()->guest(route('login'))->with('info', 'سجّل الدخول لإتمام شراء المسار.');
        }

        $amount = (float) ($path->price ?? 0);

        return view('public.catalog-checkout', $this->checkoutViewData(
            kind: 'learning_path',
            title: $path->title(),
            priceLabel: $path->formattedPrice(2) ?: format_money($amount),
            amount: $amount,
            currency: platform_currency(),
            isFree: $amount < 0.01,
            formAction: route('public.learning-paths.checkout.store', $path->slug),
            backUrl: route('public.learning-paths.show', $path->slug),
            subtitle: $path->summary() ?: null,
            durationLabel: null,
            benefits: [],
        ));
    }

    public function storePath(Request $request, string $slug): RedirectResponse
    {
        $path = LearningPath::query()->published()->where('slug', $slug)->firstOrFail();
        abort_unless($path->isStandaloneProduct(), 404);
        $amount = (float) ($path->price ?? 0);

        return $this->storeCatalogOrder($request, [
            'order_type' => Order::TYPE_LEARNING_PATH,
            'package_id' => null,
            'learning_path_id' => $path->id,
            'amount' => $amount,
            'currency' => platform_currency(),
            'title' => $path->title(),
            'is_free' => $amount < 0.01,
            'success_route' => 'student.learning-paths.show',
            'success_params' => ['slug' => $path->slug],
            'retry_route' => ['public.learning-paths.checkout', $path->slug],
        ]);
    }

    /**
     * @param  array<string, mixed>  $catalog
     */
    private function storeCatalogOrder(Request $request, array $catalog): RedirectResponse
    {
        if (! Auth::check()) {
            return redirect()->guest(route('login'));
        }

        $data = $request->validate(array_merge([
            'payment_method' => ['required', 'in:online,bank_transfer,paypal,kashier'],
        ], $request->input('payment_method') === 'bank_transfer'
            ? PlatformPaymentAccountService::manualPaymentRules(requireProof: true)
            : [
                'wallet_id' => ['nullable'],
                'payment_proof' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            ]
        ), PlatformPaymentAccountService::manualPaymentMessages());

        $method = $data['payment_method'];
        if (in_array($method, ['paypal', 'kashier'], true)) {
            $method = 'online';
            $gateway = $data['payment_method'];
        } else {
            $gateway = $method === 'online' ? 'auto' : null;
        }

        if (! empty($catalog['is_free'])) {
            return $this->activateFreeOrder($catalog);
        }

        $order = $this->upsertPendingOrder($catalog, $method, $request, $data);

        if ($method === 'bank_transfer') {
            return redirect()
                ->route('orders.show', $order)
                ->with('success', 'تم تسجيل الطلب على الحساب المختار مع إثبات التحويل. سنفعّل الوصول بعد المراجعة.');
        }

        // online
        if ($gateway === 'paypal' || ($gateway === 'auto' && PayPalSettings::isReady())) {
            return app(PayPalCheckoutController::class)->startExistingOrder($request, $order);
        }

        if ($gateway === 'kashier' || ($gateway === 'auto' && KashierSettings::isReady())) {
            return $this->redirectKashier($order, (string) $catalog['title']);
        }

        if (PaymentGatewaySettings::isFawaterakEnabled()) {
            return redirect()
                ->route('orders.show', $order)
                ->with('info', 'الطلب جاهز — أكمل الدفع من صفحة الطلب أو تواصل مع الدعم.');
        }

        return redirect()
            ->route('orders.show', $order)
            ->with('error', 'لا توجد بوابة دفع جاهزة. يمكنك التحويل البنكي أو التواصل مع الدعم.');
    }

    /**
     * @param  array<string, mixed>  $catalog
     */
    private function activateFreeOrder(array $catalog): RedirectResponse
    {
        $order = DB::transaction(function () use ($catalog) {
            $order = Order::create([
                'user_id' => Auth::id(),
                'order_type' => $catalog['order_type'],
                'package_id' => $catalog['package_id'],
                'learning_path_id' => $catalog['learning_path_id'],
                'original_amount' => 0,
                'amount' => 0,
                'currency' => $catalog['currency'],
                'payment_method' => 'other',
                'status' => Order::STATUS_APPROVED,
                'approved_at' => now(),
                'notes' => 'تفعيل مجاني — رحلة شراء كتالوج',
            ]);
            CatalogOrderFulfillmentService::fulfill($order, Auth::user());

            return $order;
        });

        event(new \App\Events\OrderStatusChanged($order->fresh(['user']), Order::STATUS_PENDING, Order::STATUS_APPROVED, 'تفعيل مجاني'));

        $route = $catalog['success_route'];
        $params = $catalog['success_params'] ?? [];

        return redirect()->route($route, $params)->with('success', 'تم تفعيل الوصول بنجاح.');
    }

    /**
     * @param  array<string, mixed>  $catalog
     */
    private function upsertPendingOrder(array $catalog, string $paymentMethod, Request $request, array $data = []): Order
    {
        $query = Order::query()
            ->where('user_id', Auth::id())
            ->where('status', Order::STATUS_PENDING)
            ->where('order_type', $catalog['order_type']);

        if ($catalog['package_id']) {
            $query->where('package_id', $catalog['package_id']);
        }
        if ($catalog['learning_path_id']) {
            $query->where('learning_path_id', $catalog['learning_path_id']);
        }

        $payload = [
            'order_type' => $catalog['order_type'],
            'package_id' => $catalog['package_id'],
            'learning_path_id' => $catalog['learning_path_id'],
            'original_amount' => $catalog['amount'],
            'amount' => $catalog['amount'],
            'currency' => $catalog['currency'],
            'payment_method' => $paymentMethod,
            'status' => Order::STATUS_PENDING,
        ];

        if ($paymentMethod === 'bank_transfer') {
            $payload['wallet_id'] = (int) ($data['wallet_id'] ?? $request->input('wallet_id'));
            if ($request->hasFile('payment_proof')) {
                $payload['payment_proof'] = $request->file('payment_proof')->store('payment-proofs', 'public');
            }
        } else {
            $payload['wallet_id'] = null;
        }

        $existing = $query->latest('id')->first();
        if ($existing) {
            $existing->update($payload);

            return $existing->fresh();
        }

        return Order::create(array_merge($payload, ['user_id' => Auth::id()]));
    }

    private function redirectKashier(Order $order, string $title): RedirectResponse
    {
        if (! KashierSettings::isReady()) {
            return back()->with('error', 'بوابة كاشير غير جاهزة.');
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

            return back()->with('error', $e->getMessage() ?: 'تعذّر فتح كاشير.');
        }

        return redirect()->away($sessionUrl);
    }

    /**
     * @return array<string, mixed>
     */
    /**
     * @param  list<string>  $benefits
     * @return array<string, mixed>
     */
    private function checkoutViewData(
        string $kind,
        string $title,
        string $priceLabel,
        float $amount,
        string $currency,
        bool $isFree,
        string $formAction,
        string $backUrl,
        ?string $subtitle = null,
        ?string $durationLabel = null,
        array $benefits = [],
    ): array {
        return [
            'kind' => $kind,
            'title' => $title,
            'priceLabel' => $priceLabel,
            'amount' => $amount,
            'currency' => $currency,
            'isFree' => $isFree,
            'formAction' => $formAction,
            'backUrl' => $backUrl,
            'subtitle' => $subtitle,
            'durationLabel' => $durationLabel,
            'benefits' => $benefits,
            'paypalReady' => PayPalSettings::isReady(),
            'kashierReady' => KashierSettings::isReady(),
            'platformAccounts' => PlatformPaymentAccountService::activeAccounts(),
            'laslesNavActive' => $kind === 'package' ? 'pricing' : 'teacher-paths',
            'pageTitle' => __('public.checkout_page_label').' — '.$title,
            'bodyClass' => 'lasles-checkout-page',
        ];
    }
}
