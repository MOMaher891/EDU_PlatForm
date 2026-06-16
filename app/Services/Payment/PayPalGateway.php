<?php

namespace App\Services\Payment;

use App\Contracts\PaymentGatewayInterface;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PayPalGateway implements PaymentGatewayInterface
{
    protected $clientId;
    protected $clientSecret;
    protected $mode;
    protected $currency;

    public function __construct()
    {
        $this->clientId = config('payment.gateways.paypal.client_id');
        $this->clientSecret = config('payment.gateways.paypal.client_secret');
        $this->mode = config('payment.gateways.paypal.mode', 'sandbox');
        $this->currency = config('payment.gateways.paypal.currency', 'USD');
    }

    /**
     * @inheritDoc
     */
    public function initiatePayment(Payment $payment, Request $request): array
    {
        try {
            if (empty($this->clientId) || empty($this->clientSecret)) {
                throw new \Exception('PayPal credentials are not configured.');
            }

            Log::info('Creating PayPal payment', [
                'payment_id' => $payment->id,
                'amount' => $payment->amount,
                'currency' => $this->currency
            ]);

            // Real-world implementation would call PayPal API to generate checkout link.
            // Using a simulator redirect for sandbox/testing.
            $redirectUrl = route('payment.callback', ['gateway' => 'paypal']) . '?payment_id=' . $payment->id . '&status=succeeded';

            return [
                'success' => true,
                'data' => [
                    'redirect_url' => $redirectUrl,
                    'payment_id' => 'PAYPAL-' . $payment->id
                ]
            ];
        } catch (\Exception $e) {
            Log::error('PayPal payment error', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage()
            ]);
            return [
                'success' => false,
                'message' => 'PayPal payment failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * @inheritDoc
     */
    public function handleCallback(Request $request): array
    {
        Log::info('PayPal callback received', $request->all());
        
        $paymentId = $request->input('payment_id');
        $status = $request->input('status');

        if ($status === 'succeeded') {
            return [
                'success' => true,
                'payment_id' => $paymentId,
                'transaction_data' => [
                    'paypal_status' => 'completed',
                    'completed_at' => now(),
                    'verified_via' => 'callback'
                ]
            ];
        }

        return [
            'success' => false,
            'payment_id' => $paymentId,
            'message' => 'PayPal transaction failed or cancelled.'
        ];
    }

    /**
     * @inheritDoc
     */
    public function handleWebhook(Request $request): array
    {
        Log::info('PayPal webhook received', $request->all());

        return [
            'success' => true,
            'payment_id' => $request->input('resource.id') ?? $request->input('id'),
            'transaction_data' => [
                'paypal_status' => 'completed',
                'completed_at' => now(),
                'verified_via' => 'webhook'
            ]
        ];
    }
}
