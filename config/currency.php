<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Platform currency (TADRIS LAB)
    |--------------------------------------------------------------------------
    | Single source of truth for catalogue prices, wallets, admin displays,
    | and checkout defaults. Payment gateways may override via their own env
    | keys but should default to this code when supported.
    */
    'code' => env('APP_CURRENCY', 'QAR'),
    'symbol' => env('APP_CURRENCY_SYMBOL', 'ر.ق'),
    'label' => env('APP_CURRENCY_LABEL', 'ريال قطري'),

    'allowed' => array_values(array_filter(array_map(
        'trim',
        explode(',', (string) env('APP_CURRENCIES', 'QAR,USD,EGP,EUR,GBP,SAR'))
    ))),
];
