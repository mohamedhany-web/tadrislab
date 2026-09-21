<?php

if (! function_exists('platform_currency')) {
    /**
     * Canonical storefront / ledger currency (ISO 4217).
     */
    function platform_currency(): string
    {
        $code = strtoupper(trim((string) config('currency.code', 'QAR')));

        return $code !== '' ? $code : 'QAR';
    }
}

if (! function_exists('currency_symbol')) {
    function currency_symbol(): string
    {
        $symbol = trim((string) config('currency.symbol', 'ر.ق'));

        return $symbol !== '' ? $symbol : platform_currency();
    }
}

if (! function_exists('currency_label')) {
    function currency_label(): string
    {
        $label = trim((string) config('currency.label', 'ريال قطري'));

        return $label !== '' ? $label : platform_currency();
    }
}

if (! function_exists('platform_currencies')) {
    /**
     * Currencies accepted for catalogue, checkout, and wallets.
     *
     * @return list<string>
     */
    function platform_currencies(): array
    {
        $configured = config('currency.allowed', ['QAR', 'USD', 'EGP', 'EUR', 'GBP', 'SAR']);
        $list = is_array($configured) ? $configured : ['QAR', 'USD', 'EGP', 'EUR', 'GBP', 'SAR'];
        $list = array_values(array_unique(array_filter(array_map(
            static fn ($c) => strtoupper(trim((string) $c)),
            $list
        ))));

        $platform = platform_currency();
        if (! in_array($platform, $list, true)) {
            array_unshift($list, $platform);
        }

        return $list !== [] ? $list : [$platform];
    }
}

if (! function_exists('normalize_currency')) {
    /**
     * Normalize an incoming currency code; falls back to platform currency.
     */
    function normalize_currency(?string $currency = null): string
    {
        $code = strtoupper(trim((string) ($currency ?: '')));
        if ($code !== '' && in_array($code, platform_currencies(), true)) {
            return $code;
        }

        return platform_currency();
    }
}

if (! function_exists('format_money')) {
    /**
     * Format an amount with the platform (or given) currency code.
     */
    function format_money(float|int|string|null $amount, ?string $currency = null, ?int $decimals = 2): string
    {
        $value = round((float) ($amount ?? 0), $decimals ?? 2);
        $code = normalize_currency($currency);
        $precision = $decimals ?? 2;

        return number_format($value, $precision).' '.$code;
    }
}
