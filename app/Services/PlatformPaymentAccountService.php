<?php

namespace App\Services;

use App\Models\Wallet;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

/**
 * حسابات التحويل اليدوي للمنصة (فودافون / إنستا / بنكي) — ليست محافظ رصيد الطلاب.
 */
class PlatformPaymentAccountService
{
    public const TYPES = ['vodafone_cash', 'instapay', 'bank_transfer'];

    public static function query(): Builder
    {
        $q = Wallet::query()->whereIn('type', self::TYPES);

        if (Schema::hasColumn('wallets', 'user_id')) {
            // حسابات المنصة: user_id فارغ (أو كانت مربوطة خطأ بأدمن سابقًا — نعرض كل أنواع الاستقبال النشطة)
            $q->where(function (Builder $inner) {
                $inner->whereNull('user_id')
                    ->orWhereIn('type', self::TYPES);
            });
        }

        return $q;
    }

    public static function activeAccounts(): Collection
    {
        return self::query()
            ->where('is_active', true)
            ->orderBy('type')
            ->orderBy('name')
            ->get();
    }

    public static function adminManageQuery(): Builder
    {
        // إدارة الحسابات: كل صفوف أنواع الاستقبال (يفضّل user_id null عند الإنشاء)
        return Wallet::query()
            ->whereIn('type', self::TYPES)
            ->orderByDesc('is_active')
            ->orderBy('name');
    }

    public static function isPlatformAccount(Wallet $wallet): bool
    {
        return in_array((string) $wallet->type, self::TYPES, true);
    }

    public static function checkoutLabel(Wallet $wallet): string
    {
        $type = Wallet::typeLabel($wallet->type);
        $number = $wallet->account_number ?: '—';
        $holder = $wallet->account_holder ? ' · '.$wallet->account_holder : '';

        return trim(($wallet->name ?: $type).' — '.$number.$holder);
    }

    /**
     * قواعد التحقق المشتركة لكل checkouts اليدوية.
     *
     * @return array<string, mixed>
     */
    public static function manualPaymentRules(bool $requireProof = true): array
    {
        $walletRule = [
            'required',
            'integer',
            Rule::exists('wallets', 'id')->where(function ($q) {
                $q->where('is_active', true)->whereIn('type', self::TYPES);
            }),
        ];

        $proof = $requireProof
            ? ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120']
            : ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'];

        return [
            'wallet_id' => $walletRule,
            'payment_proof' => $proof,
        ];
    }

    public static function manualPaymentMessages(): array
    {
        return [
            'wallet_id.required' => 'اختر الحساب الذي حوّلت إليه المبلغ.',
            'wallet_id.exists' => 'الحساب المختار غير متاح.',
            'payment_proof.required' => 'أرفق صورة أو ملف إثبات التحويل.',
            'payment_proof.mimes' => 'الإثبات يجب أن يكون صورة أو PDF.',
            'payment_proof.max' => 'حجم الإثبات بحد أقصى 5 ميجابايت.',
        ];
    }
}
