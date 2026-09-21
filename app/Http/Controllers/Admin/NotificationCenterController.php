<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Notifications\NotificationTemplateStore;
use App\Services\Notifications\PlatformNotificationDispatcher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationCenterController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = $request->user();
            if (! $user || (! $user->isAdmin()
                && ! $user->hasPermission('manage.notifications')
                && ! $user->hasPermission('manage.system-settings'))) {
                abort(403);
            }

            return $next($request);
        });
    }

    public function index(PlatformNotificationDispatcher $dispatcher): View
    {
        $recentDeliveries = collect();
        if (\Illuminate\Support\Facades\Schema::hasTable('notification_deliveries')) {
            $recentDeliveries = \App\Models\NotificationDelivery::query()
                ->latest('id')
                ->limit(40)
                ->get();
        }

        return view('admin.notification-center.index', [
            'enabled' => NotificationTemplateStore::isLayerEnabled(),
            'channels' => $dispatcher->activeChannelKeys(),
            'events' => config('platform.notifications.events', []),
            'templates' => NotificationTemplateStore::templates(),
            'recentDeliveries' => $recentDeliveries,
        ]);
    }

    public function templates(): View
    {
        return view('admin.notification-center.templates', [
            'enabled' => NotificationTemplateStore::isLayerEnabled(),
            'channels' => NotificationTemplateStore::channels(),
            'mvpChannels' => config('platform.notifications.channels_mvp', ['whatsapp', 'email']),
            'laterChannels' => config('platform.notifications.channels_later', ['sms', 'push']),
            'templates' => NotificationTemplateStore::templates(),
            'eventLabels' => [
                'payment_successful' => 'Payment Successful',
                'payment_failed' => 'Payment Failed',
                'booking_confirmed' => 'Booking Confirmed',
                'booking_reminder' => 'Booking Reminder',
                'order_status_changed' => 'Order Status Changed',
                'access_subscription_activated' => 'Access / Subscription Activated',
                'new_inquiry' => 'New Inquiry',
                'institution_program_status_changed' => 'Institution / School Program Status Changed',
            ],
        ]);
    }

    public function updateTemplates(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'enabled' => ['nullable'],
            'channels' => ['nullable', 'array'],
            'channels.*' => ['string', 'max:32'],
            'events' => ['nullable', 'array'],
            'events.*.subject' => ['nullable', 'string', 'max:255'],
            'events.*.body' => ['nullable', 'string', 'max:20000'],
        ]);

        NotificationTemplateStore::saveEnabled($request->boolean('enabled'));
        NotificationTemplateStore::saveChannels($data['channels'] ?? ['whatsapp', 'email']);
        NotificationTemplateStore::saveTemplates($data['events'] ?? []);

        return redirect()
            ->route('admin.notification-center.templates')
            ->with('success', 'تم حفظ إعدادات وقوالب الإشعارات دون الحاجة لتعديل الكود.');
    }
}
