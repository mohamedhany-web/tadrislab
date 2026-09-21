<?php

return [
    /*
    |--------------------------------------------------------------------------
    | مزود البث الافتراضي للمنصة — LiveKit فقط
    |--------------------------------------------------------------------------
    */
    'provider' => 'livekit',

    'livekit' => [
        // عنوان WebSocket العام للمتصفح (بعد DNS + SSL على الـ VPS)
        'url' => env('LIVEKIT_URL', 'wss://live.tadrislab.com'),
        // النطاق العام بدون بروتوكول (للوحة الإدارة واختبار الاتصال)
        'host' => env('LIVEKIT_PUBLIC_HOST', 'live.tadrislab.com'),
        'api_key' => env('LIVEKIT_API_KEY'),
        'api_secret' => env('LIVEKIT_API_SECRET'),
        // مدة صلاحية توكن الانضمام بالثواني
        'token_ttl' => (int) env('LIVEKIT_TOKEN_TTL', 21600),
        // عنوان HTTP داخلي/مباشر لفحص الصحة (قبل اكتمال DNS)
        'http_url' => env('LIVEKIT_HTTP_URL', 'http://187.124.36.228:7880'),
    ],
];
