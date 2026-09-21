<?php

namespace App\Services\TeacherAssistant;

use App\Contracts\TeacherAssistantProvider;

/**
 * Deterministic template provider — works offline until an AI engine is plugged in.
 */
class TemplateTeacherAssistantProvider implements TeacherAssistantProvider
{
    public function name(): string
    {
        return 'template';
    }

    public function generate(array $input): array
    {
        $lesson = trim((string) ($input['lesson_name'] ?? ''));
        $subject = trim((string) ($input['subject'] ?? '')) ?: null;
        $grade = trim((string) ($input['grade'] ?? '')) ?: null;
        $locale = ($input['locale'] ?? app()->getLocale()) === 'en' ? 'en' : 'ar';

        $ctx = $this->contextPhrase($lesson, $subject, $grade, $locale);

        if ($locale === 'en') {
            return [
                'warmup' => "2-minute energy check: students share one word they associate with {$ctx}.",
                'opener' => "Hook with a real classroom moment: «What usually goes wrong when we teach {$lesson}?».",
                'strategy' => 'Use gradual release (I do → We do → You do) with one clear success criterion on the board.',
                'activity' => "Collaborative task: pairs design a 3-step mini-plan to apply {$ctx} in tomorrow's lesson.",
                'check_understanding' => "Exit tickets: (1) Name one practice you'll try. (2) Name one obstacle. (3) What support do you need for {$lesson}?",
                'assessment_idea' => 'Peer feedback rubric (clarity / engagement / timing) — 1–2 minutes each.',
                'closure' => "Close with a commitment: write one actionable change for {$ctx}, then share with a partner.",
                'meta' => [
                    'provider' => $this->name(),
                    'generated_at' => now()->toIso8601String(),
                ],
            ];
        }

        return [
            'warmup' => "تهيئة دقيقتين: كل معلم يذكر كلمة واحدة ترتبط بـ{$ctx}.",
            'opener' => "افتتاح بموقف صفّي: «ما الذي يتعطّل عادة عند تقديم «{$lesson}»؟» ثم اربط بالممارسة المطلوبة.",
            'strategy' => 'إطلاق متدرّج (أوضّح → نجرّب معًا → يطبّقون) مع معيار نجاح واحد واضح على اللوحة.',
            'activity' => "نشاط تشاركي: ثنائيات تضع خطة من 3 خطوات لتطبيق {$ctx} في حصة الغد.",
            'check_understanding' => "تذاكر خروج: (١) ممارسة سأجربها. (٢) عائق متوقع. (٣) دعم أحتاجه حول «{$lesson}».",
            'assessment_idea' => 'تقييم أقران سريع بمعيار (وضوح / تفاعل / زمن) — دقيقة إلى دقيقتين لكل زميل.',
            'closure' => "خاتمة بالتزام: اكتب تغيّرًا واحدًا قابلاً للتنفيذ في {$ctx}، ثم شاركه مع زميل.",
            'meta' => [
                'provider' => $this->name(),
                'generated_at' => now()->toIso8601String(),
            ],
        ];
    }

    private function contextPhrase(string $lesson, ?string $subject, ?string $grade, string $locale): string
    {
        $parts = array_filter([$lesson, $subject, $grade]);
        $joined = implode($locale === 'en' ? ' / ' : ' — ', $parts);

        return $joined !== '' ? $joined : ($locale === 'en' ? 'this lesson' : 'هذا الدرس');
    }
}
