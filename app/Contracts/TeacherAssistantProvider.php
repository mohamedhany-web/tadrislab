<?php

namespace App\Contracts;

/**
 * AI-ready contract for Teacher Assistant (مساعد المعلم).
 * Swap providers without changing controllers/views.
 *
 * @phpstan-type TaInput array{lesson_name: string, subject?: ?string, grade?: ?string, locale?: string}
 * @phpstan-type TaOutput array{
 *   warmup: string,
 *   opener: string,
 *   strategy: string,
 *   activity: string,
 *   check_understanding: string,
 *   assessment_idea: string,
 *   closure: string,
 *   meta?: array{provider: string, generated_at: string}
 * }
 */
interface TeacherAssistantProvider
{
    /**
     * @param  TaInput  $input
     * @return TaOutput
     */
    public function generate(array $input): array;

    public function name(): string;
}
