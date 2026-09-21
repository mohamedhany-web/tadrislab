<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AdminPanelBranding
{
    public const SETTING_KEY = 'admin_panel_logo_path';

    private const LOGO_URL_CACHE_KEY = 'branding.admin_panel_logo_url_v3';

    /**
     * قرص التخزين: public محلي، أو r2 لـ Cloudflare R2.
     */
    public static function resolvedDisk(): string
    {
        $d = (string) config('filesystems.admin_branding_disk', 'public');

        if ($d === 'r2') {
            $bucket = config('filesystems.disks.r2.bucket');
            $endpoint = config('filesystems.disks.r2.endpoint');
            if (empty($bucket) || empty($endpoint)) {
                Log::warning('ADMIN_BRANDING_DISK=r2 لكن إعدادات R2 غير مكتملة؛ يُستخدم القرص public.');

                return 'public';
            }
        }

        if ($d === 's3') {
            $bucket = config('filesystems.disks.s3.bucket');
            if (empty($bucket)) {
                return 'public';
            }
        }

        if (! in_array($d, ['public', 'r2', 's3'], true)) {
            return 'public';
        }

        return $d;
    }

    public static function forgetLogoUrlCache(): void
    {
        Cache::forget(self::LOGO_URL_CACHE_KEY);
    }

    /**
     * رابط عرض الشعار (مُخزَّن مؤقتاً لتسريع كل صفحة).
     * الترتيب: شعار مرفوع → site/logo.png → شعار Lasles العام → SVG احتياطي TADRIS.
     */
    public static function logoPublicUrl(): ?string
    {
        return Cache::remember(self::LOGO_URL_CACHE_KEY, 3600, function () {
            $path = Setting::getValue(self::SETTING_KEY);
            if (is_string($path) && $path !== '') {
                $url = self::urlForStoredPath($path);
                if ($url !== null) {
                    return $url;
                }
            }

            // TADRIS LAB public brand mark (Lasles) before inherited Glottical site/logo.png
            $publicMark = public_path('img/lasles/logo-mark.png');
            if (is_file($publicMark)) {
                return asset('img/lasles/logo-mark.png');
            }

            $publicSvg = public_path('img/lasles/logo-mark.svg');
            if (is_file($publicSvg)) {
                return asset('img/lasles/logo-mark.svg');
            }

            $defaultPath = \App\Providers\AppServiceProvider::SITE_LOGO_STORAGE_PATH;
            $defaultUrl = self::urlForStoredPath($defaultPath);
            if ($defaultUrl !== null) {
                return $defaultUrl;
            }

            return self::inlineFallbackDataUri();
        });
    }

    /**
     * شعار احتياطي مضمّن بهوية TADRIS LAB (أزرق Lasles) — لا يعتمد على ملف في public/.
     */
    public static function inlineFallbackDataUri(): string
    {
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64" fill="none">'
            .'<rect width="64" height="64" rx="16" fill="#1E4E8C"/>'
            .'<path d="M18 20h28v6.5H36.2V44h-8.4V26.5H18V20z" fill="#FFFFFF"/>'
            .'<circle cx="46" cy="46" r="6" fill="#A88050"/>'
            .'</svg>';

        return 'data:image/svg+xml;base64,'.base64_encode($svg);
    }

    /**
     * رابط عرض صالح للمتصفح (R2 عام أو موقّع أو /storage/ بروكسي).
     */
    private static function urlForStoredPath(string $path): ?string
    {
        $path = str_replace('\\', '/', ltrim($path, '/'));

        return PublicStorageUrl::fromPath($path, self::resolvedDisk());
    }

    public static function removeLogo(): void
    {
        $path = Setting::getValue(self::SETTING_KEY);
        if (is_string($path) && $path !== '') {
            self::deletePhysicalFile($path);
        }
        Setting::setValue(self::SETTING_KEY, null);
        self::forgetLogoUrlCache();
    }

    public static function storeLogo(UploadedFile $file): void
    {
        $oldPath = Setting::getValue(self::SETTING_KEY);
        $stored = PublicMediaStorage::store($file, 'site', is_string($oldPath) ? $oldPath : null);

        Setting::setValue(self::SETTING_KEY, $stored);

        if (is_string($oldPath) && $oldPath !== '' && $oldPath !== $stored) {
            self::deletePhysicalFile($oldPath);
        }

        self::forgetLogoUrlCache();
    }

    private static function deletePhysicalFile(string $path): void
    {
        PublicMediaStorage::delete($path);
    }
}
