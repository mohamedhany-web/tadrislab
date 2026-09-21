<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Align catalogue / wallet currency defaults with TADRIS LAB platform currency (QAR).
 * Does not invent FX conversion — only renames the stored currency code from USD→QAR.
 */
return new class extends Migration
{
    public function up(): void
    {
        $target = strtoupper((string) (config('currency.code') ?: 'QAR'));
        if ($target === '' || $target === 'USD') {
            $target = 'QAR';
        }

        $tables = [
            'wallets',
            'packages',
            'service_packages',
            'tutoring_groups',
            'tutoring_group_packages',
            'orders',
            'payments',
            'transactions',
            'invoices',
            'expenses',
            'consultation_requests',
            'consultation_services',
        ];

        foreach ($tables as $table) {
            if (! Schema::hasTable($table) || ! Schema::hasColumn($table, 'currency')) {
                continue;
            }
            DB::table($table)->where('currency', 'USD')->update(['currency' => $target]);
            DB::table($table)->whereNull('currency')->update(['currency' => $target]);
        }

        if (Schema::hasTable('settings')) {
            DB::table('settings')
                ->whereIn('key', [
                    'app_currency',
                    'currency',
                    'currency_code',
                    'paypal_currency',
                    'kashier_currency',
                    'fawaterak_currency',
                ])
                ->where('value', 'USD')
                ->update(['value' => $target]);
        }
    }

    public function down(): void
    {
        // Irreversible without FX; leave QAR data in place.
    }
};
