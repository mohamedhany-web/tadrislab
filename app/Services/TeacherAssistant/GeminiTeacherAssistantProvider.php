<?php

namespace App\Services\TeacherAssistant;

use App\Contracts\TeacherAssistantProvider;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * Optional Gemini-backed provider. Falls back is handled by TeacherAssistantService.
 */
class GeminiTeacherAssistantProvider implements TeacherAssistantProvider
{
    public function name(): string
    {
        return 'gemini';
    }

    public function generate(array $input): array
    {
        if (! config('muallimx_ai.enabled') || blank(config('muallimx_ai.api_key'))) {
            throw new RuntimeException('Gemini provider is not configured.');
        }

        $lesson = trim((string) ($input['lesson_name'] ?? ''));
        $subject = trim((string) ($input['subject'] ?? ''));
        $grade = trim((string) ($input['grade'] ?? ''));
        $locale = ($input['locale'] ?? 'ar') === 'en' ? 'en' : 'ar';

        $prompt = $locale === 'en'
            ? "You are a teacher professional-development coach. Return ONLY valid JSON with keys: warmup, opener, strategy, activity, check_understanding, assessment_idea, closure. Short practical classroom ideas for lesson \"{$lesson}\", subject \"{$subject}\", grade \"{$grade}\"."
            : "أنت مدرب تطوير مهني للمعلمين. أعد JSON فقط بالمفاتيح: warmup, opener, strategy, activity, check_understanding, assessment_idea, closure. أفكار صفية عملية قصيرة لدرس «{$lesson}»، مادة «{$subject}»، صف «{$grade}».";

        $model = config('muallimx_ai.model', 'gemini-flash-latest');
        $base = rtrim((string) config('muallimx_ai.base_url'), '/');
        $url = $base.'/models/'.$model.':generateContent';

        $response = Http::timeout((int) config('muallimx_ai.http_timeout', 60))
            ->withQueryParameters(['key' => config('muallimx_ai.api_key')])
            ->post($url, [
                'contents' => [
                    ['parts' => [['text' => $prompt]]],
                ],
                'generationConfig' => [
                    'temperature' => 0.7,
                    'maxOutputTokens' => min(2048, (int) config('muallimx_ai.max_output_tokens', 8192)),
                    'responseMimeType' => 'application/json',
                ],
            ]);

        if (! $response->successful()) {
            Log::warning('TeacherAssistant Gemini failed', ['status' => $response->status(), 'body' => $response->body()]);
            throw new RuntimeException('AI generation failed.');
        }

        $text = data_get($response->json(), 'candidates.0.content.parts.0.text', '');
        $decoded = json_decode((string) $text, true);
        if (! is_array($decoded)) {
            throw new RuntimeException('AI returned invalid JSON.');
        }

        $keys = ['warmup', 'opener', 'strategy', 'activity', 'check_understanding', 'assessment_idea', 'closure'];
        $out = [];
        foreach ($keys as $key) {
            $out[$key] = trim((string) ($decoded[$key] ?? ''));
            if ($out[$key] === '') {
                throw new RuntimeException("AI missing key: {$key}");
            }
        }
        $out['meta'] = [
            'provider' => $this->name(),
            'generated_at' => now()->toIso8601String(),
        ];

        return $out;
    }
}
