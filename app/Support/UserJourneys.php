<?php

namespace App\Support;

use Illuminate\Support\Facades\Route;

/**
 * Brief V3 user-journey registry (config/journeys.php).
 */
final class UserJourneys
{
    /**
     * @return array<string, array<string, mixed>>
     */
    public static function all(): array
    {
        return config('journeys', []);
    }

    /**
     * @return array<string, mixed>|null
     */
    public static function get(string $key): ?array
    {
        $journey = config("journeys.{$key}");

        return is_array($journey) ? $journey : null;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function stepsWithUrls(string $key): array
    {
        $journey = self::get($key);
        if (! $journey) {
            return [];
        }

        $out = [];
        foreach ($journey['steps'] ?? [] as $step) {
            $route = $step['route'] ?? null;
            $url = null;
            if (is_string($route) && Route::has($route)) {
                try {
                    $url = route($route);
                } catch (\Throwable) {
                    $url = null;
                }
            }
            $out[] = array_merge($step, ['url' => $url]);
        }

        return $out;
    }
}
