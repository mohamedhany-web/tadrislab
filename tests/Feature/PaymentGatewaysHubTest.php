<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use App\Services\KashierSettings;
use App\Services\PayPalSettings;
use App\Services\PaymentGatewaySettings;
use Illuminate\Support\Facades\Hash;
use Tests\Support\BuildsFeatureSchema;
use Tests\TestCase;

class PaymentGatewaysHubTest extends TestCase
{
    use BuildsFeatureSchema;

    protected function setUp(): void
    {
        parent::setUp();
        $this->buildFeatureSchema();
    }

    public function test_kashier_settings_save_enable_and_credentials(): void
    {
        KashierSettings::save([
            'enabled' => true,
            'mode' => 'test',
            'mid' => 'MID-test',
            'api_key' => 'api-key-test',
            'secret' => 'secret-value',
            'currency' => 'USD',
            'merchant_redirect_url' => 'https://tadrislab.test/checkout/kashier/callback',
        ]);

        $this->assertTrue(KashierSettings::isEnabled());
        $this->assertTrue(KashierSettings::isConfigured());
        $this->assertTrue(KashierSettings::isReady());
        $this->assertSame('test', KashierSettings::mode());
        $this->assertSame('MID-test', KashierSettings::mid());
        $this->assertSame('api-key-test', KashierSettings::apiKey());
        $this->assertSame('secret-value', KashierSettings::secret());
        $this->assertNotSame('secret-value', Setting::getValue(KashierSettings::SECRET_KEY));
        $this->assertNotSame('api-key-test', Setting::getValue(KashierSettings::API_KEY_KEY));
        $this->assertTrue(PaymentGatewaySettings::blocksManualCheckout());
        $this->assertTrue(PaymentGatewaySettings::paidMatchesOrder(100.5, 'EGP', 100.50, 'egp'));
        $this->assertFalse(PaymentGatewaySettings::paidMatchesOrder(100.5, 'EGP', 1.00, 'USD'));
    }

    public function test_admin_payment_gateways_page_does_not_echo_kashier_api_key(): void
    {
        KashierSettings::save([
            'enabled' => true,
            'mode' => 'test',
            'mid' => 'MID-test',
            'api_key' => 'kashier-api-secret-value',
            'secret' => 'secret-value',
            'currency' => 'USD',
        ]);

        $admin = User::factory()->create([
            'role' => 'super_admin',
            'is_active' => true,
            'password' => Hash::make('password'),
        ]);

        $this->actingAs($admin)
            ->get(route('admin.payment-gateways.index'))
            ->assertOk()
            ->assertDontSee('kashier-api-secret-value', false);
    }

    public function test_admin_can_open_payment_gateways_page(): void
    {
        $admin = User::factory()->create([
            'role' => 'super_admin',
            'is_active' => true,
            'password' => Hash::make('password'),
        ]);

        $this->actingAs($admin)
            ->get(route('admin.payment-gateways.index'))
            ->assertOk()
            ->assertSee('مركز بوابات الدفع', false)
            ->assertSee('PayPal Developer Dashboard', false)
            ->assertSee('تشغيل كاشير', false);
    }

    public function test_admin_can_toggle_gateways_from_hub(): void
    {
        $admin = User::factory()->create([
            'role' => 'super_admin',
            'is_active' => true,
            'password' => Hash::make('password'),
        ]);

        PayPalSettings::save([
            'enabled' => false,
            'mode' => 'sandbox',
            'client_id' => 'cid',
            'client_secret' => 'csecret',
            'currency' => 'USD',
        ]);

        $this->actingAs($admin)
            ->put(route('admin.payment-gateways.update'), [
                'fawaterak_gateway_enabled' => '1',
                'paypal_gateway_enabled' => '1',
                'paypal_mode' => 'sandbox',
                'paypal_currency' => 'USD',
                'paypal_client_id' => 'cid',
                'paypal_client_secret' => '',
                'kashier_gateway_enabled' => '0',
                'kashier_mode' => 'test',
                'kashier_currency' => 'EGP',
                'kashier_mid' => '',
                'kashier_api_key' => '',
                'payment_gateway_fee_percent' => '2.5',
            ])
            ->assertRedirect(route('admin.payment-gateways.index'));

        $this->assertTrue(PaymentGatewaySettings::isFawaterakEnabled());
        $this->assertTrue(PayPalSettings::isEnabled());
        $this->assertFalse(KashierSettings::isEnabled());
        $this->assertSame(2.5, PaymentGatewaySettings::getFeePercent());
    }
}
