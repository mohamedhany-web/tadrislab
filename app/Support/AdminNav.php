<?php

namespace App\Support;

use Illuminate\Support\Facades\Route;

/**
 * Resolves Brief V3 admin nav sections for the current user.
 */
final class AdminNav
{
    /**
     * Sections grouped under localized hub labels for the sidebar.
     *
     * @return list<array{hub: string, label: string, sections: list<array<string, mixed>>}>
     */
    public static function hubsFor(?object $user = null): array
    {
        $sections = self::sectionsFor($user);
        if ($sections === []) {
            return [];
        }

        $hubCatalog = config('admin_nav.hubs', []);
        $grouped = [];

        foreach ($sections as $section) {
            $hubKey = (string) ($section['hub'] ?? 'product');
            if (! isset($grouped[$hubKey])) {
                $hubMeta = is_array($hubCatalog[$hubKey] ?? null) ? $hubCatalog[$hubKey] : [];
                $grouped[$hubKey] = [
                    'hub' => $hubKey,
                    'label' => self::localizedLabel($hubMeta, $hubKey),
                    'sections' => [],
                ];
            }
            $grouped[$hubKey]['sections'][] = $section;
        }

        return array_values($grouped);
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function sectionsFor(?object $user = null): array
    {
        if (! config('admin_nav.enabled', true)) {
            return [];
        }

        $user = $user ?: auth()->user();
        if (! $user) {
            return [];
        }

        $isFull = method_exists($user, 'isAdmin') && $user->isAdmin() && ! $user->roles()->exists();
        $order = config('platform.admin_sections', array_keys(config('admin_nav.sections', [])));
        $catalog = config('admin_nav.sections', []);
        $out = [];

        foreach ($order as $key) {
            $section = $catalog[$key] ?? null;
            if (! is_array($section)) {
                continue;
            }

            $module = $section['module'] ?? null;
            if ($module && PlatformModules::disabled($module)) {
                continue;
            }

            if (! $isFull && ! self::userHasAnyPermission($user, $section['permissions'] ?? [])) {
                continue;
            }

            $items = [];
            foreach ($section['items'] ?? [] as $item) {
                $routeName = $item['route'] ?? null;
                if (! $routeName || ! Route::has($routeName)) {
                    continue;
                }
                $itemPerms = $item['permissions'] ?? ($section['permissions'] ?? []);
                if (! $isFull && ! self::userHasAnyPermission($user, $itemPerms)) {
                    continue;
                }

                $params = $item['params'] ?? [];
                $active = self::isItemActive($item);
                $items[] = [
                    'label' => self::localizedLabel($item, $routeName),
                    'url' => route($routeName, $params),
                    'active' => $active,
                ];
            }

            if ($items === []) {
                continue;
            }

            $out[] = [
                'key' => $key,
                'hub' => $section['hub'] ?? null,
                'label' => self::localizedLabel($section, $key),
                'icon' => $section['icon'] ?? 'fas fa-circle',
                'items' => $items,
                'open' => collect($items)->contains(fn ($i) => $i['active']),
            ];
        }

        return $out;
    }

    /**
     * Prefer label_en when locale is English; otherwise label_ar.
     *
     * @param  array<string, mixed>  $meta
     */
    public static function localizedLabel(array $meta, string $fallback = ''): string
    {
        $en = trim((string) ($meta['label_en'] ?? ''));
        $ar = trim((string) ($meta['label_ar'] ?? ''));

        if (app()->getLocale() === 'en') {
            return $en !== '' ? $en : ($ar !== '' ? $ar : $fallback);
        }

        return $ar !== '' ? $ar : ($en !== '' ? $en : $fallback);
    }

    /**
     * @param  list<string>  $permissions
     */
    public static function userHasAnyPermission(object $user, array $permissions): bool
    {
        if ($permissions === []) {
            return true;
        }

        if (! method_exists($user, 'hasPermission')) {
            return false;
        }

        foreach ($permissions as $permission) {
            if ($user->hasPermission($permission)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  array<string, mixed>  $item
     */
    private static function isItemActive(array $item): bool
    {
        $patterns = preg_split('/\|/', (string) ($item['route_is'] ?? $item['route'] ?? '')) ?: [];
        $routeMatch = false;
        foreach ($patterns as $pattern) {
            $pattern = trim($pattern);
            if ($pattern !== '' && request()->routeIs($pattern)) {
                $routeMatch = true;
                break;
            }
        }

        if (! $routeMatch) {
            return false;
        }

        $when = $item['active_when'] ?? null;
        if (is_array($when) && $when !== []) {
            foreach ($when as $key => $value) {
                if ((string) request($key) !== (string) $value) {
                    return false;
                }
            }

            return true;
        }

        // Kind-filtered program links: only active when query matches.
        if (array_key_exists('kind', $item['params'] ?? [])) {
            return (string) request('kind') === (string) $item['params']['kind'];
        }

        // "All programs" style links: active only when kind is absent.
        if (($item['route'] ?? '') === 'admin.institution-programs.index'
            && ! array_key_exists('kind', $item['params'] ?? [])) {
            return ! request()->filled('kind');
        }

        return true;
    }
}
