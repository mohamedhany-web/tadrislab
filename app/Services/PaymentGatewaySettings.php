<?php

namespace App\Services;

use App\Models\Setting;

class PaymentGatewaySettings
{
    public const SETTING_KEY = 'fawaterak_gateway_enabled';

    /** نسبة عمولة بوابة الدفع على المبلغ المحصّل (0–100)، تُخزَّن في إعدادات النظام */
    public const FEE_PERCENT_SETTING_KEY = 'payment_gateway_fee_percent';

    public static function isFawaterakEnabled(): bool
    {
        return Setting::getValue(self::SETTING_KEY) === '1';
    }

    public static function isPaypalEnabled(): bool
    {
        return PayPalSettings::isEnabled();
    }

    public static function isKashierEnabled(): bool
    {
        return KashierSettings::isEnabled();
    }

    /** الدفع اليدوي عبر «الحسابات» يبقى متاحًا بجانب البوابات الأونلاين. */
    public static function blocksManualCheckout(): bool
    {
        return false;
    }

    /**
     * نسبة العمولة كنسبة مئوية (مثلاً 2.5 تساوي 2.5 بالمئة من المبلغ المحصّل).
     */
    public static function getFeePercent(): float
    {
        $raw = Setting::getValue(self::FEE_PERCENT_SETTING_KEY);
        if ($raw === null || $raw === '') {
            return 0.0;
        }
        $v = (float) str_replace(',', '.', (string) $raw);

        return max(0.0, min(100.0, $v));
    }

    /**
     * @return array{fee: float, net: float}
     */
    public static function computeFeeSplit(float $grossAmount): array
    {
        if ($grossAmount <= 0) {
            return ['fee' => 0.0, 'net' => 0.0];
        }
        $pct = self::getFeePercent();
        $fee = round($grossAmount * ($pct / 100), 2);
        if ($fee > $grossAmount) {
            $fee = $grossAmount;
        }
        $net = round($grossAmount - $fee, 2);

        return ['fee' => $fee, 'net' => $net];
    }

    public static function paidMatchesOrder(
        float $orderAmount,
        string $orderCurrency,
        float $paidAmount,
        string $paidCurrency
    ): bool {
        $expectedCurrency = strtoupper(trim($orderCurrency));
        $actualCurrency = strtoupper(trim($paidCurrency));
        if ($expectedCurrency === '' || $actualCurrency === '' || $expectedCurrency !== $actualCurrency) {
            return false;
        }

        return abs(round($orderAmount, 2) - round($paidAmount, 2)) < 0.005;
    }
}
