<?php

namespace App\Services;

use App\Models\ContactMessage;
use App\Models\Inquiry;
use App\Models\Institution;
use Illuminate\Support\Facades\Auth;

/**
 * Create and normalize TADRIS LAB inquiries from contact, WhatsApp, or institution forms.
 */
class InquiryService
{
    /**
     * @param  array<string, mixed>  $payload
     */
    public static function create(array $payload): Inquiry
    {
        $type = (string) ($payload['inquiry_type'] ?? Inquiry::TYPE_GENERAL);
        if (! in_array($type, Inquiry::typeKeys(), true)) {
            $type = Inquiry::TYPE_GENERAL;
        }

        $source = (string) ($payload['source'] ?? Inquiry::SOURCE_OTHER);
        if (! array_key_exists($source, Inquiry::sourceLabels())) {
            $source = Inquiry::SOURCE_OTHER;
        }

        $userId = array_key_exists('user_id', $payload)
            ? ($payload['user_id'] ?: null)
            : null;

        $inquiry = Inquiry::create([
            'name' => trim((string) $payload['name']),
            'email' => filled($payload['email'] ?? null) ? trim((string) $payload['email']) : null,
            'phone' => filled($payload['phone'] ?? null) ? trim((string) $payload['phone']) : null,
            'inquiry_type' => $type,
            'status' => $payload['status'] ?? Inquiry::STATUS_NEW,
            'source' => $source,
            'user_id' => $userId,
            'order_id' => $payload['order_id'] ?? null,
            'consultation_request_id' => $payload['consultation_request_id'] ?? null,
            'institution_id' => $payload['institution_id'] ?? null,
            'institution_program_id' => $payload['institution_program_id'] ?? null,
            'contact_message_id' => $payload['contact_message_id'] ?? null,
            'subject' => filled($payload['subject'] ?? null) ? trim((string) $payload['subject']) : null,
            'message' => filled($payload['message'] ?? null) ? trim((string) $payload['message']) : null,
            'admin_notes' => filled($payload['admin_notes'] ?? null) ? trim((string) $payload['admin_notes']) : null,
            'assigned_to' => $payload['assigned_to'] ?? null,
            'inquired_at' => $payload['inquired_at'] ?? now(),
        ]);

        try {
            event(new \App\Events\NewInquiry($inquiry));
        } catch (\Throwable $e) {
            report($e);
        }

        return $inquiry;
    }

    /**
     * @param  array<string, mixed>  $validated
     */
    public static function fromContactForm(array $validated, ?ContactMessage $contactMessage = null): Inquiry
    {
        $topic = (string) ($validated['topic'] ?? $validated['inquiry_type'] ?? '');
        $type = self::mapTopicToType($topic);

        return self::create([
            'name' => $validated['name'],
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'inquiry_type' => $validated['inquiry_type'] ?? $type,
            'subject' => $validated['subject'] ?? ($validated['topic'] ?? null),
            'message' => $validated['message'] ?? null,
            'source' => Inquiry::SOURCE_CONTACT,
            'contact_message_id' => $contactMessage?->id,
            'user_id' => Auth::id(),
            'order_id' => $validated['order_id'] ?? null,
            'consultation_request_id' => $validated['consultation_request_id'] ?? null,
            'institution_id' => $validated['institution_id'] ?? null,
        ]);
    }

    /**
     * Manual / provider intake when conversation starts on WhatsApp.
     *
     * @param  array<string, mixed>  $payload
     */
    public static function fromWhatsApp(array $payload): Inquiry
    {
        $payload['source'] = Inquiry::SOURCE_WHATSAPP;

        return self::create($payload);
    }

    public static function fromInstitutionInquiry(
        Institution $institution,
        string $contactName,
        ?string $message = null,
        ?string $serviceLabel = null,
        ?int $institutionProgramId = null
    ): Inquiry {
        return self::create([
            'name' => $contactName,
            'email' => $institution->contact_email,
            'phone' => $institution->contact_phone,
            'inquiry_type' => Inquiry::TYPE_SCHOOL_INSTITUTION,
            'subject' => $serviceLabel ?: ('استفسار مؤسسة: '.$institution->name_ar),
            'message' => $message,
            'source' => Inquiry::SOURCE_INSTITUTION,
            'institution_id' => $institution->id,
            'institution_program_id' => $institutionProgramId,
            'user_id' => Auth::id() ?: null,
        ]);
    }

    public static function mapTopicToType(?string $topic): string
    {
        $t = mb_strtolower(trim((string) $topic));
        if ($t === '') {
            return Inquiry::TYPE_GENERAL;
        }

        if (in_array($t, Inquiry::typeKeys(), true)) {
            return $t;
        }

        return match (true) {
            str_contains($t, 'path') || str_contains($t, 'مسار') => Inquiry::TYPE_LEARNING_PATH,
            str_contains($t, 'package') || str_contains($t, 'باق') => Inquiry::TYPE_PACKAGE,
            str_contains($t, 'consult') || str_contains($t, 'استشار') => Inquiry::TYPE_CONSULTATION,
            str_contains($t, 'pay') || str_contains($t, 'دفع') || str_contains($t, 'payment') => Inquiry::TYPE_PAYMENT,
            str_contains($t, 'tech') || str_contains($t, 'تقن') => Inquiry::TYPE_TECHNICAL,
            str_contains($t, 'school') || str_contains($t, 'institution') || str_contains($t, 'مدرس') || str_contains($t, 'مؤسس') => Inquiry::TYPE_SCHOOL_INSTITUTION,
            default => Inquiry::TYPE_GENERAL,
        };
    }
}
