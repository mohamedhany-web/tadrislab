<?php

namespace App\Services\TeacherAssistant;

use App\Contracts\TeacherAssistantProvider;
use Illuminate\Support\Facades\Log;

/**
 * Facade over swappable Teacher Assistant providers (AI-ready).
 */
class TeacherAssistantService
{
    public function __construct(
        private readonly TemplateTeacherAssistantProvider $template,
        private readonly GeminiTeacherAssistantProvider $gemini,
    ) {}

    /**
     * @param  array{lesson_name: string, subject?: ?string, grade?: ?string, locale?: string}  $input
     * @return array<string, mixed>
     */
    public function generate(array $input): array
    {
        $provider = $this->resolveProvider();

        try {
            return $provider->generate($input);
        } catch (\Throwable $e) {
            if ($provider->name() === 'template') {
                throw $e;
            }
            Log::info('TeacherAssistant falling back to template', ['error' => $e->getMessage()]);

            return $this->template->generate($input);
        }
    }

    public function resolveProvider(): TeacherAssistantProvider
    {
        $configured = config('platform.teacher_assistant.ai_provider')
            ?: env('TADRIS_TA_AI_PROVIDER');

        if ($configured === 'gemini' && config('muallimx_ai.enabled') && filled(config('muallimx_ai.api_key'))) {
            return $this->gemini;
        }

        return $this->template;
    }

    /** @return array<string, array{ar: string, en: string}> */
    public static function outputLabels(): array
    {
        return config('platform.teacher_assistant.outputs', []);
    }
}
