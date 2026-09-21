<?php

namespace App\Services\Notifications;

use App\Contracts\PlatformNotifiableEvent;
use App\Models\NotificationDelivery;
use App\Services\Notifications\Channels\EmailChannel;
use App\Services\Notifications\Channels\NotificationChannel;
use App\Services\Notifications\Channels\PushChannel;
use App\Services\Notifications\Channels\SmsChannel;
use App\Services\Notifications\Channels\WhatsAppChannel;
use App\Support\Notifications\NotificationRecipient;
use App\Support\PlatformModules;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * Central Notification Layer — Events → templates → WhatsApp/Email (SMS/Push later).
 */
class PlatformNotificationDispatcher
{
    /** @var array<string, NotificationChannel> */
    private array $channels = [];

    public function __construct(
        EmailChannel $email,
        WhatsAppChannel $whatsapp,
        SmsChannel $sms,
        PushChannel $push,
    ) {
        foreach ([$email, $whatsapp, $sms, $push] as $channel) {
            $this->channels[$channel->key()] = $channel;
        }
    }

    /**
     * @return array{event: string, delivered: int, failed: int, skipped: int}
     */
    public function dispatch(PlatformNotifiableEvent $event): array
    {
        $key = $event->notificationKey();
        $stats = ['event' => $key, 'delivered' => 0, 'failed' => 0, 'skipped' => 0];

        if (! $this->isEnabled()) {
            $stats['skipped']++;

            return $stats;
        }

        if (! in_array($key, config('platform.notifications.events', []), true)) {
            Log::warning('Unknown platform notification event', ['event' => $key]);
            $stats['skipped']++;

            return $stats;
        }

        $template = NotificationTemplateStore::template($key);
        if (! is_array($template)) {
            Log::warning('Missing notification template', ['event' => $key]);
            $stats['skipped']++;

            return $stats;
        }

        $activeChannels = $this->activeChannelKeys();
        $recipients = $this->uniqueRecipients($event->recipients());

        foreach ($recipients as $recipient) {
            if (! $recipient->hasChannelTarget()) {
                $stats['skipped']++;

                continue;
            }

            $data = array_merge(
                ['name' => $recipient->displayName()],
                $event->templateData()
            );
            $subject = $this->render((string) ($template['subject'] ?? config('app.name')), $data);
            $body = $this->render((string) ($template['body'] ?? ''), $data);

            foreach ($activeChannels as $channelKey) {
                $channel = $this->channels[$channelKey] ?? null;
                if (! $channel) {
                    $stats['skipped']++;

                    continue;
                }

                $result = $channel->send($recipient, $subject, $body);
                if (! empty($result['skipped'])) {
                    $stats['skipped']++;
                    $this->recordDelivery($key, $channelKey, NotificationDelivery::STATUS_SKIPPED, $recipient, $subject, $body, $result);
                } elseif (! empty($result['success'])) {
                    $stats['delivered']++;
                    $this->recordDelivery($key, $channelKey, NotificationDelivery::STATUS_DELIVERED, $recipient, $subject, $body, $result);
                } else {
                    $stats['failed']++;
                    $this->recordDelivery($key, $channelKey, NotificationDelivery::STATUS_FAILED, $recipient, $subject, $body, $result);
                }
            }
        }

        Log::info('Platform notification dispatched', $stats);

        return $stats;
    }

    public function isEnabled(): bool
    {
        if (! NotificationTemplateStore::isLayerEnabled()) {
            return false;
        }

        if (! config('platform.mvp.whatsapp_email_notifications_core', true)) {
            return false;
        }

        return PlatformModules::enabled('notifications');
    }

    /**
     * @return list<string>
     */
    public function activeChannelKeys(): array
    {
        $configured = NotificationTemplateStore::channels();
        $out = [];
        foreach ($configured as $key) {
            $key = strtolower(trim((string) $key));
            if ($key !== '' && isset($this->channels[$key])) {
                $out[] = $key;
            }
        }

        return array_values(array_unique($out ?: ['whatsapp', 'email']));
    }

    /**
     * @param  list<NotificationRecipient>  $recipients
     * @return list<NotificationRecipient>
     */
    private function uniqueRecipients(array $recipients): array
    {
        $seen = [];
        $out = [];
        foreach ($recipients as $recipient) {
            if (! $recipient instanceof NotificationRecipient) {
                continue;
            }
            $fingerprint = strtolower(
                ($recipient->user?->id ?? '0').'|'.
                ($recipient->resolvedEmail() ?? '').'|'.
                ($recipient->resolvedPhone() ?? '')
            );
            if (isset($seen[$fingerprint])) {
                continue;
            }
            $seen[$fingerprint] = true;
            $out[] = $recipient;
        }

        return $out;
    }

    /**
     * @param  array<string, string|int|float|null>  $data
     */
    private function render(string $template, array $data): string
    {
        $replacements = [];
        foreach ($data as $key => $value) {
            $replacements[':'.$key] = (string) ($value ?? '');
        }

        return strtr($template, $replacements);
    }

    /**
     * Persist delivery attempt for expansion (audit / retries / future SMS-Push).
     *
     * @param  array<string, mixed>  $result
     */
    private function recordDelivery(
        string $eventKey,
        string $channel,
        string $status,
        NotificationRecipient $recipient,
        string $subject,
        string $body,
        array $result
    ): void {
        if (! Schema::hasTable('notification_deliveries')) {
            return;
        }

        try {
            NotificationDelivery::query()->create([
                'event_key' => $eventKey,
                'channel' => $channel,
                'status' => $status,
                'notifiable_type' => $recipient->user ? $recipient->user->getMorphClass() : null,
                'notifiable_id' => $recipient->user?->id,
                'recipient_email' => $recipient->resolvedEmail(),
                'recipient_phone' => $recipient->resolvedPhone(),
                'recipient_name' => $recipient->displayName(),
                'subject' => mb_substr($subject, 0, 255),
                'body' => mb_substr($body, 0, 5000),
                'provider_response' => $result,
                'error_message' => isset($result['error']) ? (string) $result['error'] : null,
                'sent_at' => $status === NotificationDelivery::STATUS_DELIVERED ? now() : null,
            ]);
        } catch (\Throwable $e) {
            Log::warning('notification_deliveries write failed', ['error' => $e->getMessage()]);
        }
    }
}
