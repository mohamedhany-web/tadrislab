<?php

namespace App\Support;

/**
 * Brief V3 expandable Data Model registry.
 *
 * Intent: architecture that can grow — MVP only requires entities marked mvp_required.
 * Catalog products stay separate models (no forced unified Product table in MVP).
 */
final class DataModel
{
    /**
     * @return array<string, array<string, mixed>>
     */
    public static function entities(): array
    {
        return config('platform.data_model', []);
    }

    /**
     * @return list<string>
     */
    public static function mvpRequiredKeys(): array
    {
        $keys = [];
        foreach (self::entities() as $key => $meta) {
            if (! empty($meta['mvp_required'])) {
                $keys[] = $key;
            }
        }

        return $keys;
    }

    /**
     * @return list<string>
     */
    public static function expansionKeys(): array
    {
        $keys = [];
        foreach (self::entities() as $key => $meta) {
            if (empty($meta['mvp_required'])) {
                $keys[] = $key;
            }
        }

        return $keys;
    }

    /**
     * Product/service kinds (catalog axes) — implemented as separate tables today.
     *
     * @return array<string, string>
     */
    public static function productServiceKinds(): array
    {
        return config('platform.data_model.product_service.kinds', [
            'learning_path' => \App\Models\LearningPath::class,
            'consultation' => \App\Models\ConsultationService::class,
            'training_program' => \App\Models\InstitutionProgram::class,
            'institutional_service' => \App\Models\InstitutionProgram::class,
            'resource' => \App\Models\TeacherTool::class,
        ]);
    }
}
