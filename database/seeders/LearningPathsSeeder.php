<?php

namespace Database\Seeders;

use App\Models\LearningPath;
use App\Models\LearningPathUnit;
use Illuminate\Database\Seeder;

/**
 * Skeleton catalogue only — no full lesson/tool content yet.
 * Admins fill units → lessons → practices later.
 */
class LearningPathsSeeder extends Seeder
{
    public function run(): void
    {
        $catalogue = config('platform.learning_paths', []);
        $order = 1;

        foreach ($catalogue as $key => $labels) {
            $path = LearningPath::query()->firstOrNew(['slug' => str_replace('_', '-', $key)]);
            $path->fill([
                'title_ar' => $labels['ar'] ?? $key,
                'title_en' => $labels['en'] ?? $key,
                'skill_focus_ar' => $labels['ar'] ?? null,
                'skill_focus_en' => $labels['en'] ?? null,
                'summary_ar' => $path->summary_ar ?: ('مسار عملي لتطوير مهارة «'.($labels['ar'] ?? $key).'» لدى المعلم داخل الصف.'),
                'summary_en' => $path->summary_en ?: ('A practical path to grow the teacher skill: '.($labels['en'] ?? $key).'.'),
                'sort_order' => $order++,
                'is_active' => true,
            ]);
            if (! $path->exists) {
                // Unpublished until content is ready — content seeder publishes filled paths.
                $path->is_published = false;
            }
            $path->save();

            if ($key === 'classroom_management' && $path->units()->count() === 0) {
                LearningPathUnit::query()->create([
                    'learning_path_id' => $path->id,
                    'title_ar' => 'تشخيص مناخ الصف',
                    'title_en' => 'Diagnosing classroom climate',
                    'summary_ar' => 'وحدة تمهيدية — أكمل المحتوى عبر TadrisPathContentSeeder أو من لوحة الإدارة.',
                    'summary_en' => 'Intro unit — fill via TadrisPathContentSeeder or admin.',
                    'sort_order' => 1,
                    'is_active' => true,
                ]);
            }
        }
    }
}
