<?php

namespace App\Support;

use App\Models\Package;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;

/**
 * Resolves public service-page interconnection (packages, related hubs, delivery story).
 */
final class TadrisServicePages
{
    /**
     * @return array<string, mixed>
     */
    public static function meta(string $pageKey): array
    {
        return config("tadris_services.pages.{$pageKey}", []);
    }

    /**
     * @return Collection<int, Package>
     */
    public static function packagesFor(string $pageKey): Collection
    {
        $types = self::meta($pageKey)['package_types'] ?? [];
        if ($types === [] || ! class_exists(Package::class)) {
            return collect();
        }

        try {
            return Package::query()
                ->active()
                ->whereIn('package_type', $types)
                ->orderBy('order')
                ->limit(4)
                ->get();
        } catch (\Throwable) {
            return collect();
        }
    }

    /**
     * @return list<array{key: string, label: string, url: string, lead: string}>
     */
    public static function relatedPages(string $pageKey): array
    {
        $keys = self::meta($pageKey)['related'] ?? [];
        $out = [];
        foreach ($keys as $key) {
            $node = TadrisPublicNav::find($key);
            if (! $node || empty($node['route']) || ! Route::has($node['route'])) {
                continue;
            }
            $content = __('site.pages.'.$key);
            $out[] = [
                'key' => $key,
                'label' => $node['label'],
                'url' => route($node['route']),
                'lead' => is_array($content) ? (string) ($content['lead'] ?? '') : '',
            ];
        }

        return $out;
    }

    public static function catalogUrl(string $pageKey): ?string
    {
        $routeName = self::meta($pageKey)['catalog_route'] ?? null;
        if (! $routeName || ! Route::has($routeName)) {
            return null;
        }

        return route($routeName);
    }

    public static function deliveryKey(string $pageKey): string
    {
        return (string) (self::meta($pageKey)['delivery'] ?? 'course');
    }

    public static function pillarKey(string $pageKey): ?string
    {
        $pillar = self::meta($pageKey)['pillar'] ?? null;

        return is_string($pillar) ? $pillar : null;
    }
}
