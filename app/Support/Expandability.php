<?php

namespace App\Support;

use Illuminate\Support\Facades\Route;

/**
 * Brief V3 §16 — expandability checklist (config/expandability.php).
 */
final class Expandability
{
    /**
     * @return array<string, array<string, mixed>>
     */
    public static function all(): array
    {
        return config('expandability', []);
    }

    /**
     * @return array<string, mixed>|null
     */
    public static function get(string $key): ?array
    {
        $item = config("expandability.{$key}");

        return is_array($item) ? $item : null;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function withUrls(): array
    {
        $out = [];
        foreach (self::all() as $key => $meta) {
            $route = $meta['admin_route'] ?? null;
            $url = null;
            if (is_string($route) && Route::has($route)) {
                try {
                    $url = route($route);
                } catch (\Throwable) {
                    $url = null;
                }
            }
            $out[] = array_merge(['key' => $key], $meta, ['url' => $url]);
        }

        return $out;
    }

    /**
     * @return list<string>
     */
    public static function worksKeys(): array
    {
        return array_keys(array_filter(
            self::all(),
            fn ($m) => ($m['status'] ?? '') === 'works'
        ));
    }
}
