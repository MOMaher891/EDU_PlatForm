<?php

namespace App\Services\Payment;

use App\Contracts\PaymentGatewayInterface;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Exception\ApiErrorException;

class StripeGateway implements PaymentGatewayInterface
{
    protected $secretKey;
    protected $publicKey;
    protected $webhookSecret;
    protected $currency;

    public function __construct()
    {
        $this->secretKey = config('payment.gateways.stripe.secret_key');
        $this->publicKey = config('payment.gateways.stripe.public_key');
        $this->webhookSecret = config('payment.gateways.stripe.webhook_secret');
        $this->currency = config('payment.gateways.stripe.currency', 'usd');
    }

    /**
     * @inheritDoc
     */
    public function initiatePayment(Payment $payment, Request $request): array
    {
        try {
            if (empty($this->secretKey)) {
                throw new \Exception('Stripe secret key is not configured.');
            }

            Stripe::setApiKey($this->secretKey);

            Log::info('Creating Stripe payment intent', [
                'payment_id' => $payment->id,
                'amount' => $payment->amount,
                'currency' => $this->currency
            ]);

            $paymentIntent = PaymentIntent::create([
                'amount' => (int) round($payment->amount * 100),
                'currency' => $this->currency,
                'automatic_payment_methods' => [
                    'enabled' => true,
                    'allow_redirects' => 'never'
                ],
                'metadata' => [
                    'payment_id' => $payment->id,
                    'course_id' => $payment->course_id,
                    'user_id' => $payment->user_id,
                ],
                'description' => "Course: {$payment->course->title}",
                'receipt_email' => $payment->user->email,
            ]);

            $payment->update([
                'payment_id' => $paymentIntent->id,
                'transaction_data' => array_merge($payment->transaction_data ?? [], [
                    'client_secret' => $paymentIntent->client_secret,
                    'status' => $paymentIntent->status,
                    'stripe_payment_intent_id' => $paymentIntent->id,
                    'last_updated' => now()
                ])
            ]);

            return [
                'success' => true,
                'data' => [
                    'client_secret' => $paymentIntent->client_secret,
                    'payment_intent_id' => $paymentIntent->id
                ]
            ];
        } catch (ApiErrorException $e) {
            Log::error('Stripe API error', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage()
            ]);
            return [
                'success' => false,
                'message' => 'Stripe payment failed: ' . $e->getMessage()
            ];
        } catch (\Exception $e) {
            Log::error('Stripe payment error', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage()
            ]);
            return [
                'success' => false,
                'message' => 'Stripe payment failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * @inheritDoc
     */
    public function handleCallback(Request $request): array
    {
        try {
            $paymentIntentId = $request->input('payment_intent_id') ?? $request->query('payment_intent_id');
            if (empty($paymentIntentId)) {
                return [
                    'success' => false,
                    'message' => 'Missing Stripe payment intent ID.'
                ];
            }

            if (empty($this->secretKey)) {
                throw new \Exception('Stripe secret key is not configured.');
            }

            Stripe::setApiKey($this->secretKey);
            $paymentIntent = PaymentIntent::retrieve($paymentIntentId);

            if ($paymentIntent->status === 'succeeded') {
                return [
                    'success' => true,
                    'payment_id' => $paymentIntentId,
                    'transaction_data' => [
                        'stripe_status' => $paymentIntent->status,
                        'completed_at' => now(),
                        'verified_via' => 'callback'
                    ]
                ];
            }

            return [
                'success' => false,
                'payment_id' => $paymentIntentId,
                'message' => 'Stripe payment not completed. Status: ' . $paymentIntent->status
            ];
        } catch (\Exception $e) {
            Log::error('Stripe callback confirmation failed', [
                'error' => $e->getMessage()
            ]);
            return [
                'success' => false,
                'message' => 'Stripe confirmation failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * @inheritDoc
     */
    public function handleWebhook(Request $request): array
    {
        try {
            $payload = $request->getContent();
            $sigHeader = $request->header('Stripe-Signature');

            if (empty($this->webhookSecret)) {
                return [
                    'success' => false,
                    'message' => 'Stripe webhook secret not configured.'
                ];
            }

            $event = \Stripe\Webhook::constructEvent($payload, $sigHeader, $this->webhookSecret);
            $paymentIntent = $event->data->object;

            if ($event->type === 'payment_intent.succeeded') {
                $paymentId = $paymentIntent->metadata->payment_id ?? null;
                return [
                    'success' => true,
                    'payment_id' => $paymentId ?? $paymentIntent->id,
                    'transaction_data' => [
                        'stripe_status' => $paymentIntent->status,
                        'completed_at' => now(),
                        'verified_via' => 'webhook'
                    ]
                ];
            }

            return [
                'success' => false,
                'message' => 'Unhandled event type: ' . $event->type
            ];
        } catch (\Exception $e) {
            Log::error('Stripe webhook handling failed', [
                'error' => $e->getMessage()
            ]);
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}
