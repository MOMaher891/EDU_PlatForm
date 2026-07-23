<?php

namespace App\Services;

use App\Models\Payment;
use App\Services\Payment\PaymentFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentGatewayService
{
    protected $factory;

    public function __construct(PaymentFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * Process payment using the specified gateway
     */
    public function processPayment(Payment $payment, Request $request, string $gateway = null)
    {
        try {
            $gateway = $gateway ?: config('payment.default', 'stripe');
            $strategy = $this->factory->make($gateway);
            return $strategy->initiatePayment($payment, $request);
        } catch (\Exception $e) {
            Log::error('Payment processing error', [
                'payment_id' => $payment->id,
                'gateway' => $gateway,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Payment processing failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Handle webhook from payment gateways
     */
    public function handleWebhook(Request $request, string $gateway)
    {
        try {
            $strategy = $this->factory->make($gateway);
            return $strategy->handleWebhook($request);
        } catch (\Exception $e) {
            Log::error("Webhook handling error for {$gateway}: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Handle callback redirect from payment gateways
     */
    public function handleCallback(Request $request, string $gateway)
    {
        try {
            $strategy = $this->factory->make($gateway);
            return $strategy->handleCallback($request);
        } catch (\Exception $e) {
            Log::error("Callback handling error for {$gateway}: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Confirm Stripe payment (for backward compatibility)
     */
    public function confirmStripePayment(string $paymentIntentId)
    {
        try {
            $stripeGateway = $this->factory->make('stripe');
            $request = new Request(['payment_intent_id' => $paymentIntentId]);
            $result = $stripeGateway->handleCallback($request);

            if ($result['success']) {
                return [
                    'success' => true,
                    'data' => [
                        'status' => $result['transaction_data']['stripe_status'] ?? 'succeeded',
                        'amount' => 0.0,
                        'currency' => 'usd'
                    ]
                ];
            }

            return $result;
        } catch (\Exception $e) {
            Log::error('Stripe confirmation error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Stripe confirmation failed: ' . $e->getMessage()
            ];
        }
    }
}
