<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Course;
use App\Models\CourseSection;
use App\Models\Payment;
use App\Models\CourseEnrollment;
use App\Models\StudentSectionAccess;
use App\Services\Payment\PaymentFactory;
use App\Services\Payment\PaymobGateway;
use App\Services\Payment\StripeGateway;
use App\Services\Payment\PayPalGateway;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class PaymentStrategyTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test PaymentFactory resolves correct strategies.
     */
    public function test_payment_factory_resolves_correct_strategies()
    {
        $factory = new PaymentFactory();

        $this->assertInstanceOf(PaymobGateway::class, $factory->make('paymob'));
        $this->assertInstanceOf(StripeGateway::class, $factory->make('stripe'));
        $this->assertInstanceOf(PayPalGateway::class, $factory->make('paypal'));
    }

    /**
     * Test Paymob HMAC verification for webhook.
     */
    public function test_paymob_gateway_verifies_webhook_hmac()
    {
        config(['payment.gateways.paymob.hmac_secret' => 'super_secret_hmac']);

        $gateway = new PaymobGateway();

        // Sample data matching Paymob's webhook structure
        $objData = [
            'amount_cents' => 15000,
            'created_at' => '2026-06-08T19:09:47',
            'currency' => 'EGP',
            'error_occured' => false,
            'has_parent_transaction' => false,
            'id' => 987654,
            'integration_id' => 1234,
            'is_3d_secure' => true,
            'is_auth' => false,
            'is_capture' => false,
            'is_voided' => false,
            'is_refunded' => false,
            'owner' => 4567,
            'pending' => false,
            'source_data_pan' => '1234',
            'source_data_sub_type' => 'card',
            'source_data_type' => 'visa',
            'success' => true,
            'order' => [
                'id' => 112233
            ]
        ];

        // Concatenated payload string:
        $string = '15000' . '2026-06-08T19:09:47' . 'EGP' . 'false' . 'false' . '987654' . '1234' . 'true' . 'false' . 'false' . 'false' . 'false' . '4567' . 'false' . '1234' . 'card' . 'visa' . 'true';
        $expectedHmac = hash_hmac('sha512', $string, 'super_secret_hmac');

        $request = Request::create(
            '/webhook?hmac=' . $expectedHmac,
            'POST',
            [
                'type' => 'TRANSACTION',
                'obj' => $objData
            ]
        );

        $result = $gateway->handleWebhook($request);

        $this->assertTrue($result['success']);
        $this->assertEquals('112233', $result['payment_id']);
        $this->assertEquals('succeeded', $result['transaction_data']['payment_status']);
    }

    /**
     * Test Paymob Webhook endpoint processes payment successfully and enrolls student.
     */
    public function test_paymob_webhook_completes_payment_and_enrolls_student()
    {
        config(['payment.gateways.paymob.hmac_secret' => 'super_secret_hmac']);

        $user = User::factory()->create(['role' => 'student']);
        $course = Course::factory()->create();

        // Create pending payment record
        $payment = Payment::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'amount' => 150.00,
            'currency' => 'EGP',
            'payment_method' => 'paymob',
            'payment_id' => '112233', // Paymob Order ID
            'status' => 'pending'
        ]);

        $objData = [
            'amount_cents' => 15000,
            'created_at' => '2026-06-08T19:09:47',
            'currency' => 'EGP',
            'error_occured' => false,
            'has_parent_transaction' => false,
            'id' => 987654,
            'integration_id' => 1234,
            'is_3d_secure' => true,
            'is_auth' => false,
            'is_capture' => false,
            'is_voided' => false,
            'is_refunded' => false,
            'owner' => 4567,
            'pending' => false,
            'source_data_pan' => '1234',
            'source_data_sub_type' => 'card',
            'source_data_type' => 'visa',
            'success' => true,
            'order' => [
                'id' => 112233
            ]
        ];

        $string = '15000' . '2026-06-08T19:09:47' . 'EGP' . 'false' . 'false' . '987654' . '1234' . 'true' . 'false' . 'false' . 'false' . 'false' . '4567' . 'false' . '1234' . 'card' . 'visa' . 'true';
        $expectedHmac = hash_hmac('sha512', $string, 'super_secret_hmac');

        $response = $this->postJson(route('payment.webhook', ['gateway' => 'paymob']) . '?hmac=' . $expectedHmac, [
            'type' => 'TRANSACTION',
            'obj' => $objData
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);

        // Check payment updated to completed
        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'status' => 'completed',
            'currency' => 'EGP'
        ]);

        // Check student enrolled in course
        $this->assertDatabaseHas('course_enrollments', [
            'user_id' => $user->id,
            'course_id' => $course->id
        ]);
    }

    /**
     * Test Paymob Webhook endpoint processes section payment and grants section access.
     */
    public function test_paymob_webhook_processes_section_payment_and_grants_access()
    {
        config(['payment.gateways.paymob.hmac_secret' => 'super_secret_hmac']);

        $user = User::factory()->create(['role' => 'student']);
        $course = Course::factory()->create();
        $section = CourseSection::factory()->create([
            'course_id' => $course->id,
            'price' => 50.00,
            'is_purchasable_separately' => true
        ]);

        // Create pending payment record for section
        $payment = Payment::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'amount' => 50.00,
            'currency' => 'EGP',
            'payment_method' => 'paymob',
            'payment_id' => '445566', // Paymob Order ID
            'status' => 'pending',
            'transaction_data' => [
                'section_id' => $section->id
            ]
        ]);

        $objData = [
            'amount_cents' => 5000,
            'created_at' => '2026-06-08T19:09:47',
            'currency' => 'EGP',
            'error_occured' => false,
            'has_parent_transaction' => false,
            'id' => 987654,
            'integration_id' => 1234,
            'is_3d_secure' => true,
            'is_auth' => false,
            'is_capture' => false,
            'is_voided' => false,
            'is_refunded' => false,
            'owner' => 4567,
            'pending' => false,
            'source_data_pan' => '1234',
            'source_data_sub_type' => 'card',
            'source_data_type' => 'visa',
            'success' => true,
            'order' => [
                'id' => 445566
            ]
        ];

        $string = '5000' . '2026-06-08T19:09:47' . 'EGP' . 'false' . 'false' . '987654' . '1234' . 'true' . 'false' . 'false' . 'false' . 'false' . '4567' . 'false' . '1234' . 'card' . 'visa' . 'true';
        $expectedHmac = hash_hmac('sha512', $string, 'super_secret_hmac');

        $response = $this->postJson(route('payment.webhook', ['gateway' => 'paymob']) . '?hmac=' . $expectedHmac, [
            'type' => 'TRANSACTION',
            'obj' => $objData
        ]);

        $response->assertStatus(200);

        // Check payment updated to completed
        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'status' => 'completed'
        ]);

        // Check section access granted
        $this->assertDatabaseHas('student_section_access', [
            'user_id' => $user->id,
            'section_id' => $section->id,
            'price_paid' => 50.00,
            'is_active' => true
        ]);
    }
}
