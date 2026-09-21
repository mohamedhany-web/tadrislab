<?php

namespace App\Support;

class TadrisPublicNav
{
    /** @return array<string, array> */
    public static function tree(): array
    {
        $tree = config('tadris_nav', []);

        return collect($tree)
            ->filter(fn ($node) => ($node['enabled'] ?? true) !== false)
            ->sortBy('order')
            ->all();
    }

    /** Top-level items for primary nav. */
    public static function primary(): array
    {
        return collect(self::tree())
            ->filter(fn ($node) => ($node['nav'] ?? false) === true)
            ->map(function (array $node, string $key) {
                $children = collect($node['children'] ?? [])
                    ->filter(fn ($child) => ($child['enabled'] ?? true) !== false)
                    ->map(fn (array $child, string $childKey) => self::hydrate($childKey, $child))
                    ->values()
                    ->all();

                return self::hydrate($key, $node) + ['children' => $children];
            })
            ->values()
            ->all();
    }

    /**
     * Commercial services for header (Developer Brief V3 five pillars).
     *
     * @return list<array{key: string, label: string, route: string, url: string}>
     */
    public static function services(): array
    {
        $locale = app()->getLocale();
        $pillars = config('platform.pillars', []);

        $routeMap = [
            'learning_paths' => 'public.learning-paths.index',
            'tools_resources' => 'public.tools.index',
            'consultations' => 'public.site.consultations',
            'packages' => 'public.pricing',
            'schools_institutions' => 'public.site.institutional',
        ];

        $out = [];
        foreach ($routeMap as $key => $routeName) {
            if (! \Illuminate\Support\Facades\Route::has($routeName)) {
                continue;
            }
            $pillar = $pillars[$key] ?? [];
            $label = $locale === 'ar'
                ? ($pillar['label_ar'] ?? __('site.nav.'.$key))
                : ($pillar['label_en'] ?? __('site.nav.'.$key));

            $out[] = [
                'key' => $key,
                'label' => $label,
                'route' => $routeName,
                'url' => route($routeName),
            ];
        }

        return $out;
    }

    public static function serviceIsActive(): bool
    {
        foreach (self::services() as $service) {
            if (request()->routeIs($service['route'])) {
                return true;
            }
            // Learning paths hub & children
            if ($service['key'] === 'learning_paths' && request()->routeIs([
                'public.learning-paths.*',
                'public.site.teacher-development',
                'public.site.teacher-courses',
                'public.site.teacher-resources',
                'public.path',
            ])) {
                return true;
            }
            if ($service['key'] === 'consultations' && request()->routeIs('public.site.consultations*')) {
                return true;
            }
            if ($service['key'] === 'schools_institutions' && request()->routeIs('public.site.institutional*')) {
                return true;
            }
            if ($service['key'] === 'tools_resources' && request()->routeIs(['public.tools.*', 'public.site.resources*'])) {
                return true;
            }
        }

        return false;
    }

    public static function find(string $key): ?array
    {
        $tree = config('tadris_nav', []);
        if (isset($tree[$key])) {
            return self::hydrate($key, $tree[$key]) + [
                'children' => collect($tree[$key]['children'] ?? [])
                    ->filter(fn ($c) => ($c['enabled'] ?? true) !== false)
                    ->map(fn (array $c, string $ck) => self::hydrate($ck, $c))
                    ->values()
                    ->all(),
            ];
        }

        foreach ($tree as $parentKey => $parent) {
            foreach ($parent['children'] ?? [] as $childKey => $child) {
                if ($childKey === $key) {
                    return self::hydrate($childKey, $child) + [
                        'parent' => self::hydrate($parentKey, $parent),
                        'children' => [],
                    ];
                }
            }
        }

        return null;
    }

    /** Flat list of routable nodes for registration. */
    public static function routable(): array
    {
        $out = [];
        foreach (self::tree() as $key => $node) {
            if (($node['route'] ?? null) && str_starts_with((string) $node['route'], 'public.site.')) {
                $out[$key] = self::hydrate($key, $node);
            }
            foreach ($node['children'] ?? [] as $childKey => $child) {
                if (($child['enabled'] ?? true) === false) {
                    continue;
                }
                if (($child['route'] ?? null) && str_starts_with((string) $child['route'], 'public.site.')) {
                    $out[$childKey] = self::hydrate($childKey, $child);
                }
            }
        }

        return $out;
    }

    public static function isActive(string $key): bool
    {
        $node = self::find($key);
        if (! $node) {
            return false;
        }
        $route = $node['route'] ?? null;
        if ($route && request()->routeIs($route)) {
            return true;
        }
        foreach ($node['children'] ?? [] as $child) {
            if (! empty($child['route']) && request()->routeIs($child['route'])) {
                return true;
            }
        }

        return false;
    }

    protected static function hydrate(string $key, array $node): array
    {
        return [
            'key' => $key,
            'uri' => $node['uri'] ?? '/',
            'route' => $node['route'] ?? null,
            'view' => $node['view'] ?? 'public.site.show',
            'nav' => (bool) ($node['nav'] ?? false),
            'order' => (int) ($node['order'] ?? 100),
            'enabled' => ($node['enabled'] ?? true) !== false,
            'label' => __('site.nav.'.$key),
        ];
    }
}
