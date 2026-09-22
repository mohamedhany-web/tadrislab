<?php

namespace Tests\Feature;

use App\Models\LearningPath;
use App\Models\Order;
use App\Models\Package;
use App\Models\TutoringGroup;
use App\Models\TutoringGroupPackage;
use App\Models\User;
use App\Models\Wallet;
use App\Services\KashierSettings;
use App\Services\PaymentGatewaySettings;
use App\Services\PlatformPaymentAccountService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\Support\BuildsFeatureSchema;
use Tests\TestCase;

class PlatformPaymentAccountsTest extends TestCase
{
    use BuildsFeatureSchema;

    protected function setUp(): void
    {
        parent::setUp();
        $this->buildFeatureSchema();
        $this->ensurePaymentTables();
        Storage::fake('public');
    }

    private function ensurePaymentTables(): void
    {
        if (! Schema::hasTable('wallets')) {
            Schema::create('wallets', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('name')->nullable();
                $table->string('type')->nullable();
                $table->string('account_number')->nullable();
                $table->string('bank_name')->nullable();
                $table->string('account_holder')->nullable();
                $table->text('notes')->nullable();
                $table->boolean('is_active')->default(true);
                $table->decimal('balance', 12, 2)->default(0);
                $table->decimal('pending_balance', 12, 2)->default(0);
                $table->string('currency', 8)->default('QAR');
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('wallet_transactions')) {
            Schema::create('wallet_transactions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('wallet_id');
                $table->string('type')->nullable();
                $table->decimal('amount', 12, 2)->default(0);
                $table->decimal('balance_after', 12, 2)->nullable();
                $table->string('description')->nullable();
                $table->text('notes')->nullable();
                $table->unsignedBigInteger('payment_id')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('wallet_reports')) {
            Schema::create('wallet_reports', function (Blueprint $table) {
                $table->id();
                $table->foreignId('wallet_id');
                $table->string('report_month')->nullable();
                $table->decimal('opening_balance', 12, 2)->nullable();
                $table->decimal('closing_balance', 12, 2)->nullable();
                $table->decimal('total_deposits', 12, 2)->nullable();
                $table->decimal('total_withdrawals', 12, 2)->nullable();
                $table->unsignedInteger('transactions_count')->nullable();
                $table->text('expected_amounts')->nullable();
                $table->text('actual_amounts')->nullable();
                $table->decimal('difference', 12, 2)->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('packages')) {
            Schema::create('packages', function (Blueprint $table) {
                $table->id();
                $table->string('name')->nullable();
                $table->string('slug')->nullable();
                $table->text('description')->nullable();
                $table->text('card_summary')->nullable();
                $table->json('features')->nullable();
                $table->decimal('price', 10, 2)->default(0);
                $table->decimal('original_price', 10, 2)->nullable();
                $table->string('currency', 8)->nullable();
                $table->string('track')->nullable();
                $table->string('package_type')->nullable();
                $table->string('cta_mode')->nullable();
                $table->string('thumbnail')->nullable();
                $table->integer('duration_days')->nullable();
                $table->unsignedInteger('courses_count')->nullable();
                $table->unsignedInteger('consultation_sessions')->default(0);
                $table->unsignedInteger('participant_seats')->nullable();
                $table->boolean('includes_tools')->default(false);
                $table->json('tools_resources')->nullable();
                $table->string('discount_note')->nullable();
                $table->integer('order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->boolean('is_featured')->default(false);
                $table->boolean('is_popular')->default(false);
                $table->timestamp('starts_at')->nullable();
                $table->timestamp('ends_at')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('learning_paths')) {
            Schema::create('learning_paths', function (Blueprint $table) {
                $table->id();
                $table->string('slug')->unique();
                $table->string('title_ar')->nullable();
                $table->string('title_en')->nullable();
                $table->string('skill_focus_ar')->nullable();
                $table->string('skill_focus_en')->nullable();
                $table->text('summary_ar')->nullable();
                $table->text('summary_en')->nullable();
                $table->text('description_ar')->nullable();
                $table->text('description_en')->nullable();
                $table->string('thumbnail')->nullable();
                $table->unsignedInteger('estimated_minutes')->nullable();
                $table->decimal('price', 10, 2)->default(0);
                $table->string('currency', 8)->nullable();
                $table->boolean('is_sellable_standalone')->default(true);
                $table->unsignedInteger('access_days')->nullable();
                $table->integer('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->boolean('is_published')->default(true);
                $table->unsignedBigInteger('instructor_id')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('orders')) {
            Schema::create('orders', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id');
                $table->unsignedBigInteger('advanced_course_id')->nullable();
                $table->unsignedBigInteger('tutoring_group_id')->nullable();
                $table->unsignedBigInteger('tutoring_group_package_id')->nullable();
                $table->unsignedBigInteger('tutoring_group_cohort_id')->nullable();
                $table->unsignedBigInteger('service_package_id')->nullable();
                $table->unsignedBigInteger('package_id')->nullable();
                $table->unsignedBigInteger('learning_path_id')->nullable();
                $table->json('custom_package_data')->nullable();
                $table->string('order_type')->default('course');
                $table->unsignedBigInteger('academic_year_id')->nullable();
                $table->unsignedBigInteger('coupon_id')->nullable();
                $table->decimal('original_amount', 10, 2)->nullable();
                $table->decimal('discount_amount', 10, 2)->nullable();
                $table->decimal('wallet_credit_amount', 10, 2)->nullable();
                $table->decimal('amount', 10, 2)->default(0);
                $table->string('currency', 8)->nullable();
                $table->string('billing_mode')->nullable();
                $table->boolean('auto_renew')->default(false);
                $table->string('payment_method')->nullable();
                $table->unsignedBigInteger('wallet_id')->nullable();
                $table->string('payment_proof')->nullable();
                $table->unsignedBigInteger('invoice_id')->nullable();
                $table->unsignedBigInteger('payment_id')->nullable();
                $table->string('fawaterak_invoice_id')->nullable();
                $table->string('status')->default('pending');
                $table->text('notes')->nullable();
                $table->timestamp('approved_at')->nullable();
                $table->unsignedBigInteger('approved_by')->nullable();
                $table->unsignedBigInteger('sales_owner_id')->nullable();
                $table->timestamp('sales_contacted_at')->nullable();
                $table->unsignedBigInteger('sales_lead_id')->nullable();
                $table->timestamps();
            });
        } else {
            Schema::table('orders', function (Blueprint $table) {
                if (! Schema::hasColumn('orders', 'package_id')) {
                    $table->unsignedBigInteger('package_id')->nullable();
                }
                if (! Schema::hasColumn('orders', 'learning_path_id')) {
                    $table->unsignedBigInteger('learning_path_id')->nullable();
                }
                if (! Schema::hasColumn('orders', 'wallet_id')) {
                    $table->unsignedBigInteger('wallet_id')->nullable();
                }
                if (! Schema::hasColumn('orders', 'payment_proof')) {
                    $table->string('payment_proof')->nullable();
                }
            });
        }

        if (! Schema::hasTable('tutoring_group_packages')) {
            Schema::create('tutoring_group_packages', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tutoring_group_id');
                $table->string('name');
                $table->decimal('price', 10, 2)->default(0);
                $table->decimal('original_price', 10, 2)->nullable();
                $table->unsignedInteger('sessions_count')->default(1);
                $table->unsignedInteger('duration_months')->default(1);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('advanced_courses')) {
            Schema::create('advanced_courses', function (Blueprint $table) {
                $table->id();
                $table->string('title')->nullable();
                $table->string('slug')->nullable();
                $table->decimal('price', 10, 2)->default(0);
                $table->string('currency', 8)->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('activity_logs')) {
            Schema::create('activity_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('action')->nullable();
                $table->string('model_type')->nullable();
                $table->unsignedBigInteger('model_id')->nullable();
                $table->text('description')->nullable();
                $table->json('properties')->nullable();
                $table->timestamps();
            });
        }
    }

    private function makePlatformAccount(array $overrides = []): Wallet
    {
        return Wallet::create(array_merge([
            'user_id' => null,
            'name' => 'حساب تحويل المنصة',
            'type' => 'vodafone_cash',
            'account_number' => '01001234567',
            'account_holder' => 'TADRIS LAB',
            'is_active' => true,
            'balance' => 0,
            'pending_balance' => 0,
            'currency' => 'QAR',
        ], $overrides));
    }

    private function makeLearner(): User
    {
        return User::factory()->create([
            'role' => 'student',
            'is_active' => true,
            'password' => Hash::make('password'),
        ]);
    }

    public function test_manual_checkout_stays_open_when_online_gateway_enabled(): void
    {
        KashierSettings::save([
            'enabled' => true,
            'mode' => 'test',
            'mid' => 'MID-test',
            'api_key' => 'api-key-test',
            'secret' => 'secret-value',
            'currency' => 'QAR',
        ]);

        $this->assertTrue(KashierSettings::isReady());
        $this->assertFalse(PaymentGatewaySettings::blocksManualCheckout());
    }

    public function test_platform_accounts_service_lists_active_receiving_accounts(): void
    {
        $active = $this->makePlatformAccount(['name' => 'نشط']);
        $this->makePlatformAccount(['name' => 'متوقف', 'is_active' => false]);
        Wallet::create([
            'user_id' => $this->makeLearner()->id,
            'name' => 'رصيد طالب',
            'type' => null,
            'is_active' => true,
            'balance' => 50,
            'pending_balance' => 0,
            'currency' => 'QAR',
        ]);

        $accounts = PlatformPaymentAccountService::activeAccounts();

        $this->assertTrue($accounts->contains('id', $active->id));
        $this->assertCount(1, $accounts);
        $this->assertStringContainsString('01001234567', $active->checkoutLabel());
    }

    public function test_admin_can_create_platform_account_with_transfer_details(): void
    {
        $admin = User::factory()->create([
            'role' => 'super_admin',
            'is_active' => true,
            'password' => Hash::make('password'),
        ]);

        $this->actingAs($admin)
            ->post(route('admin.wallets.store'), [
                'name' => 'إنستا باي المنصة',
                'type' => 'instapay',
                'account_number' => 'lab@instapay',
                'account_holder' => 'TADRIS LAB',
                'bank_name' => null,
                'balance' => 0,
                'notes' => 'للتحويل اليدوي',
                'is_active' => '1',
            ])
            ->assertRedirect(route('admin.wallets.index'));

        $this->assertDatabaseHas('wallets', [
            'name' => 'إنستا باي المنصة',
            'type' => 'instapay',
            'account_number' => 'lab@instapay',
            'account_holder' => 'TADRIS LAB',
            'user_id' => null,
            'is_active' => 1,
        ]);
    }

    public function test_admin_accounts_index_uses_accounts_label(): void
    {
        $admin = User::factory()->create([
            'role' => 'super_admin',
            'is_active' => true,
            'password' => Hash::make('password'),
        ]);
        $this->makePlatformAccount();

        $this->actingAs($admin)
            ->get(route('admin.wallets.index'))
            ->assertOk()
            ->assertSee('الحسابات', false);
    }

    public function test_catalog_package_manual_payment_requires_account_and_stores_proof(): void
    {
        $learner = $this->makeLearner();
        $account = $this->makePlatformAccount();
        $package = Package::create([
            'name' => 'باقة اختبار',
            'slug' => 'pkg-accounts-test',
            'price' => 199,
            'currency' => 'QAR',
            'package_type' => Package::TYPE_INDIVIDUAL,
            'cta_mode' => 'checkout',
            'is_active' => true,
            'order' => 1,
        ]);

        $this->actingAs($learner)
            ->post(route('public.packages.checkout.store', $package->slug), [
                'payment_method' => 'bank_transfer',
            ])
            ->assertSessionHasErrors(['wallet_id', 'payment_proof']);

        $proof = UploadedFile::fake()->image('transfer.jpg');

        $response = $this->actingAs($learner)
            ->post(route('public.packages.checkout.store', $package->slug), [
                'payment_method' => 'bank_transfer',
                'wallet_id' => $account->id,
                'payment_proof' => $proof,
            ]);

        $order = Order::query()->where('user_id', $learner->id)->where('package_id', $package->id)->first();
        $this->assertNotNull($order);
        $response->assertRedirect(route('orders.show', $order));

        $this->assertSame('bank_transfer', $order->payment_method);
        $this->assertSame(Order::STATUS_PENDING, $order->status);
        $this->assertSame((int) $account->id, (int) $order->wallet_id);
        $this->assertNotEmpty($order->payment_proof);
        Storage::disk('public')->assertExists($order->payment_proof);
    }

    public function test_catalog_path_manual_payment_binds_account(): void
    {
        $learner = $this->makeLearner();
        $account = $this->makePlatformAccount(['type' => 'bank_transfer', 'account_number' => 'EG123']);
        $path = LearningPath::create([
            'slug' => 'path-accounts-test',
            'title_ar' => 'مسار اختبار',
            'title_en' => 'Test path',
            'price' => 80,
            'currency' => 'QAR',
            'is_sellable_standalone' => true,
            'is_active' => true,
            'is_published' => true,
            'sort_order' => 1,
        ]);

        $proof = UploadedFile::fake()->create('receipt.pdf', 120, 'application/pdf');

        $this->actingAs($learner)
            ->post(route('public.learning-paths.checkout.store', $path->slug), [
                'payment_method' => 'bank_transfer',
                'wallet_id' => $account->id,
                'payment_proof' => $proof,
            ])
            ->assertRedirect();

        $order = Order::query()
            ->where('user_id', $learner->id)
            ->where('learning_path_id', $path->id)
            ->first();

        $this->assertNotNull($order);
        $this->assertSame((int) $account->id, (int) $order->wallet_id);
        $this->assertSame('bank_transfer', $order->payment_method);
        $this->assertNotEmpty($order->payment_proof);
    }

    public function test_tutoring_manual_checkout_requires_account_and_proof(): void
    {
        config(['platform.modules.tutoring.enabled' => true]);

        $learner = $this->makeLearner();
        $instructor = User::factory()->create([
            'role' => 'instructor',
            'is_active' => true,
            'password' => Hash::make('password'),
        ]);
        $account = $this->makePlatformAccount(['type' => 'instapay']);

        $group = TutoringGroup::create([
            'type' => 'individual',
            'title' => 'مجموعة اختبار',
            'slug' => 'group-accounts-test',
            'instructor_id' => $instructor->id,
            'price' => 150,
            'currency' => 'QAR',
            'is_active' => true,
            'capacity' => 1,
            'duration_minutes' => 60,
        ]);

        $package = TutoringGroupPackage::create([
            'tutoring_group_id' => $group->id,
            'name' => 'باقة حصص',
            'price' => 150,
            'sessions_count' => 4,
            'duration_months' => 1,
            'is_active' => true,
        ]);

        $this->actingAs($learner)
            ->post(route('public.groups.checkout.store', $group->slug), [
                'package_id' => $package->id,
                'payment_method' => 'bank_transfer',
            ])
            ->assertSessionHasErrors(['wallet_id', 'payment_proof']);

        $proof = UploadedFile::fake()->image('tutoring-proof.png');

        $response = $this->actingAs($learner)
            ->post(route('public.groups.checkout.store', $group->slug), [
                'package_id' => $package->id,
                'payment_method' => 'bank_transfer',
                'wallet_id' => $account->id,
                'payment_proof' => $proof,
            ]);

        $order = Order::query()
            ->where('user_id', $learner->id)
            ->where('tutoring_group_package_id', $package->id)
            ->first();

        $this->assertNotNull($order);
        $response->assertRedirect(route('orders.show', $order));
        $this->assertSame((int) $account->id, (int) $order->wallet_id);
        $this->assertSame('bank_transfer', $order->payment_method);
        $this->assertNotEmpty($order->payment_proof);
    }

    public function test_catalog_checkout_page_lists_platform_accounts(): void
    {
        $learner = $this->makeLearner();
        $account = $this->makePlatformAccount([
            'name' => 'فودافون كاش المنصة',
            'account_number' => '01119998887',
        ]);
        $package = Package::create([
            'name' => 'باقة عرض',
            'slug' => 'pkg-accounts-show',
            'price' => 50,
            'currency' => 'QAR',
            'package_type' => Package::TYPE_INDIVIDUAL,
            'cta_mode' => 'checkout',
            'is_active' => true,
            'order' => 1,
        ]);

        $this->actingAs($learner)
            ->get(route('public.packages.checkout', $package->slug))
            ->assertOk()
            ->assertSee('فودافون كاش المنصة', false)
            ->assertSee('01119998887', false)
            ->assertSee((string) $account->id, false);
    }
}
