<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application for file storage.
    |
    */

    'default' => env('FILESYSTEM_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Below you may configure as many filesystem disks as necessary, and you
    | may even configure multiple disks for the same driver. Examples for
    | most supported storage drivers are configured here for reference.
    |
    | Supported drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app/private'),
            'serve' => false, // Disabled so custom /storage route is used (fixes 404 on shared hosting)
            'throw' => false,
            'report' => false,
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
            'throw' => false,
            'report' => false,
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
            'report' => false,
        ],

        /*
         * Cloudflare R2 — متوافق مع واجهة S3.
         * يُستخدم لرفع ملفات مجتمع الذكاء الاصطناعي (تقديمات المساهمين).
         */
        // للرفع المباشر من لوحة «هيكل المناهج»: CORS على الـ bucket — AllowedMethods PUT، AllowedOrigins = https://نطاقك، AllowedHeaders * أو x-amz-*، ExposeHeaders: ETag (بدونها يفشل الـ multipart بعد نجاح الجزء).
        // مسار multipart-proxy-part (احتياطي عبر Laravel): اضبط nginx client_max_body_size و PHP post_max_size ≥ حجم الجزء (upload_limits.curriculum_r2_multipart_part_bytes).
        // للرفع عبر PHP فقط: nginx client_max_body_size و proxy_read_timeout و Cloudflare (حدود الحجم/الوقت) — الملفات الكبيرة تفضل الرفع المباشر لتجنب ERR_HTTP2.
        'r2' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION', 'auto'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', true),
            // R2 لا يدعم Object ACL (public-read) إلا إذا فُعّل يدوياً. العرض العام عبر R2_PUBLIC_URL.
            'visibility' => 'private',
            'throw' => false,
            'report' => false,
            'http' => [
                'connect_timeout' => 15,
                'timeout' => 90,
            ],
        ],

        /*
         * تسجيلات جلسات البث المباشر (Jibri → R2).
         * يمكن استخدام نفس R2 أو bucket منفصل عبر R2_LIVE_RECORDINGS_* في .env
         */
        // للرفع المباشر من المتصفح: أضف في R2 → CORS للـ bucket: AllowedMethods PUT,GET,HEAD و AllowedOrigins = نطاق الموقع.
        'live_recordings_r2' => [
            'driver' => 's3',
            'key' => env('R2_LIVE_RECORDINGS_ACCESS_KEY_ID', env('AWS_ACCESS_KEY_ID')),
            'secret' => env('R2_LIVE_RECORDINGS_SECRET_ACCESS_KEY', env('AWS_SECRET_ACCESS_KEY')),
            'region' => env('R2_LIVE_RECORDINGS_REGION', 'auto'),
            'bucket' => env('R2_LIVE_RECORDINGS_BUCKET', env('AWS_BUCKET')),
            'endpoint' => env('R2_LIVE_RECORDINGS_ENDPOINT', env('AWS_ENDPOINT')),
            'use_path_style_endpoint' => true,
            'throw' => false,
            'report' => false,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | قرص ملفات المجتمع (مساهمون + أدمن)
    |--------------------------------------------------------------------------
    | استخدم 'r2' لرفع الملفات على Cloudflare R2، أو 'local' للتطوير المحلي.
    | بعد تغيير .env نفّذ: php artisan config:clear
    */
    'community_disk' => env('FILESYSTEM_DISK_COMMUNITY', 'local'),

    /*
    |--------------------------------------------------------------------------
    | وسائط الموقع العامة (كورسات، باقات، مسارات، سلايدر، …)
    |--------------------------------------------------------------------------
    | public = storage/app/public + /storage/...
    | r2     = Cloudflare R2 (AWS_* ويفضّل R2_PUBLIC_URL أو AWS_URL للرابط المباشر)
    */
    'public_media_disk' => env('PUBLIC_MEDIA_DISK', 'public'),

    /*
    |--------------------------------------------------------------------------
    | رابط R2 العام للمتصفح (ليس cloudflarestorage.com)
    |--------------------------------------------------------------------------
    | من Cloudflare R2 → bucket → Public access → r2.dev
    | مثال: https://pub-xxxxxxxx.r2.dev
    */
    'r2_public_url' => env('R2_PUBLIC_URL'),

    /*
    |--------------------------------------------------------------------------
    | شعار لوحة التحكم (Admin)
    |--------------------------------------------------------------------------
    | public = storage/app/public + رابط /storage (محلياً: نفّذ php artisan storage:link)
    | r2     = Cloudflare R2 (نفس مفاتيح AWS_* و AWS_URL في .env)
    */
    'admin_branding_disk' => env('ADMIN_BRANDING_DISK', env('PUBLIC_MEDIA_DISK', 'public')),

    /*
    |--------------------------------------------------------------------------
    | صور خدمات الموقع العامة (/services)
    |--------------------------------------------------------------------------
    | public = storage/app/public + /storage/...
    | r2     = Cloudflare R2 (نفس AWS_* و AWS_URL و AWS_ENDPOINT)
    */
    'site_services_disk' => env('SITE_SERVICES_DISK', env('PUBLIC_MEDIA_DISK', 'public')),

    /*
    |--------------------------------------------------------------------------
    | صور آراء الموقع (الصفحة الرئيسية)
    |--------------------------------------------------------------------------
    | إن لم تُضبط SITE_TESTIMONIALS_DISK يُستخدم SITE_SERVICES_DISK ثم public.
    */
    'site_testimonials_disk' => env('SITE_TESTIMONIALS_DISK') ?: env('SITE_SERVICES_DISK', env('PUBLIC_MEDIA_DISK', 'public')),

    /*
    |--------------------------------------------------------------------------
    | صور بورتفوليو الطلاب (my-portfolio)
    |--------------------------------------------------------------------------
    | public = storage/app/public + /storage/... (php artisan storage:link)
    | r2     = Cloudflare R2 (نفس AWS_* و AWS_URL و AWS_ENDPOINT)
    */
    'portfolio_disk' => env('PORTFOLIO_DISK', env('PUBLIC_MEDIA_DISK', 'public')),

    /*
    |--------------------------------------------------------------------------
    | مرفقات الواجبات (ملفات المدرب + تسليم الطالب)
    |--------------------------------------------------------------------------
    | public = storage/app/public + /storage/...  |  r2 = Cloudflare R2 (AWS_* في .env)
    | إن تُرك ASSIGNMENT_FILES_DISK فارغاً يُستخدم PORTFOLIO_DISK ثم public.
    */
    'assignment_files_disk' => env('ASSIGNMENT_FILES_DISK') ?: env('PORTFOLIO_DISK', 'public'),

    /*
    |--------------------------------------------------------------------------
    | مكتبة الماتريال (ملفات المحاضرات / lecture-materials)
    |--------------------------------------------------------------------------
    | r2     = Cloudflare R2 (افتراضي — يتبع PUBLIC_MEDIA_DISK إن تُرك فارغاً)
    | public = تخزين محلي للتطوير فقط
    */
    'lecture_materials_disk' => env('LECTURE_MATERIALS_DISK') ?: env('PUBLIC_MEDIA_DISK', 'public'),

    /*
    |--------------------------------------------------------------------------
    | مواد المناهج التفاعلية (PPTX / PDF في هيكل المنهج)
    |--------------------------------------------------------------------------
    | r2     = Cloudflare R2 (افتراضي)
    | public = تخزين محلي للتطوير فقط عند غياب مفاتيح AWS_*
    */
    'curriculum_library_disk' => env('CURRICULUM_LIBRARY_DISK', env('PUBLIC_MEDIA_DISK', 'public')),

    /*
    |--------------------------------------------------------------------------
    | صور الملف الشخصي للمستخدمين (profile_image)
    |--------------------------------------------------------------------------
    | إن تُرك USER_PROFILE_DISK فارغاً يُستخدم PORTFOLIO_DISK ثم public — لتوحيد R2 مع معرض المحتوى.
    */
    'user_profile_disk' => env('USER_PROFILE_DISK', env('PORTFOLIO_DISK', 'public')),

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Here you may configure the symbolic links that will be created when the
    | `storage:link` Artisan command is executed. The array keys should be
    | the locations of the links and the values should be their targets.
    |
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
