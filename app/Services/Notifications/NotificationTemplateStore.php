<?php

namespace App\Services\Notifications;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

/**
 * Admin-editable overrides for notification templates/channels (no code deploy).
 */
class NotificationTemplateStore
{
    public const TEMPLATES_KEY = 'tadris_notification_templates_json';

    public const CHANNELS_KEY = 'tadris_notification_channels';

    public const ENABLED_KEY = 'tadris_notifications_enabled';

    /**
     * @return array<string, array{subject: string, body: string}>
     */
    public static function templates(): array
    {
        $defaults = config('notifications.events', []);
        $overrides = self::decodeOverrides();

        $out = [];
        foreach ($defaults as $key => $tpl) {
            $override = $overrides[$key] ?? [];
            $out[$key] = [
                'subject' => (string) ($override['subject'] ?? $tpl['subject'] ?? ''),
                'body' => (string) ($override['body'] ?? $tpl['body'] ?? ''),
                'overridden' => isset($overrides[$key]),
            ];
        }

        return $out;
    }

    /**
     * @return array{subject: string, body: string}|null
     */
    public static function template(string $eventKey): ?array
    {
        $all = self::templates();

        return $all[$eventKey] ?? null;
    }

    /**
     * @param  array<string, array{subject?: string, body?: string}>  $events
     */
    public static function saveTemplates(array $events): void
    {
        $defaults = config('notifications.events', []);
        $clean = [];
        foreach ($events as $key => $tpl) {
            if (! isset($defaults[$key]) || ! is_array($tpl)) {
                continue;
            }
            $subject = trim((string) ($tpl['subject'] ?? ''));
            $body = trim((string) ($tpl['body'] ?? ''));
            $defaultSubject = trim((string) ($defaults[$key]['subject'] ?? ''));
            $defaultBody = trim((string) ($defaults[$key]['body'] ?? ''));
            // Persist only when different from defaults (or both filled)
            if ($subject === '' && $body === '') {
                continue;
            }
            if ($subject === $defaultSubject && $body === $defaultBody) {
                continue;
            }
            $clean[$key] = [
                'subject' => $subject !== '' ? $subject : $defaultSubject,
                'body' => $body !== '' ? $body : $defaultBody,
            ];
        }

        Setting::setValue(self::TEMPLATES_KEY, $clean === [] ? null : json_encode($clean, JSON_UNESCAPED_UNICODE));
        Cache::forget('tadris.notification_templates');
    }

    /**
     * @return list<string>
     */
    public static function channels(): array
    {
        $stored = Setting::getValue(self::CHANNELS_KEY);
        if (filled($stored)) {
            return array_values(array_filter(array_map('trim', explode(',', $stored))));
        }

        $configured = config('notifications.channels', ['whatsapp', 'email']);

        return is_array($configured) ? array_values($configured) : ['whatsapp', 'email'];
    }

    /**
     * @param  list<string>  $channels
     */
    public static function saveChannels(array $channels): void
    {
        $allowed = array_merge(
            config('platform.notifications.channels_mvp', ['whatsapp', 'email']),
            config('platform.notifications.channels_later', ['sms', 'push'])
        );
        $clean = [];
        foreach ($channels as $ch) {
            $ch = strtolower(trim((string) $ch));
            if (in_array($ch, $allowed, true)) {
                $clean[] = $ch;
            }
        }
        $clean = array_values(array_unique($clean ?: ['whatsapp', 'email']));
        Setting::setValue(self::CHANNELS_KEY, implode(',', $clean));
        Cache::forget('tadris.notification_channels');
    }

    public static function isLayerEnabled(): bool
    {
        $stored = Setting::getValue(self::ENABLED_KEY);
        if ($stored === null) {
            return (bool) config('notifications.enabled', true);
        }

        return in_array(strtolower($stored), ['1', 'true', 'yes', 'on'], true);
    }

    public static function saveEnabled(bool $enabled): void
    {
        Setting::setValue(self::ENABLED_KEY, $enabled ? '1' : '0');
    }

    /**
     * @return array<string, array{subject: string, body: string}>
     */
    private static function decodeOverrides(): array
    {
        return Cache::remember('tadris.notification_templates', 60, function () {
            $raw = Setting::getValue(self::TEMPLATES_KEY);
            if (! filled($raw)) {
                return [];
            }
            $decoded = json_decode($raw, true);

            return is_array($decoded) ? $decoded : [];
        });
    }
}
