<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Exception\ApiErrorException;

class PaymentGatewayService
{
    protected $config;

    public function __construct()
    {
        try {
            $this->config = config('payment');
        } catch (\Exception $e) {
            Log::error('Error in PaymentGatewayService constructor: ' , [
                'trace' => $e->getTraceAsString()
            ]);
            $this->config = [];
        }
    }

    /**
     * Process payment using the specified gateway
     */
    public function processPayment(Payment $payment, Request $request, string $gateway = null)
    {
        try {
            $gateway = $gateway ?: $this->config['default'] ?? 'stripe';

            if (!$this->isGatewayEnabled($gateway)) {
                return [
                    'success' => false,
                    'message' => "Gateway {$gateway} is not enabled"
                ];
            }

            switch ($gateway) {
                case 'stripe':
                    return $this->processStripePayment($payment, $request);
                case 'paypal':
                    return $this->processPayPalPayment($payment, $request);
                case 'paymob':
                    return $this->processPayMobPayment($payment, $request);
                default:
                    return [
                        'success' => false,
                        'message' => "Unsupported payment gateway: {$gateway}"
                    ];
            }
        } catch (\Exception $e) {
            Log::error('Payment processing error', [
                'payment_id' => $payment->id,
                'gateway' => $gateway,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Payment processing failed: '
            ];
        }
    }

    /**
     * Process Stripe payment
     */
    protected function processStripePayment(Payment $payment, Request $request)
    {
        try {
            // Check if Stripe is configured
            if (empty($this->config['gateways']['stripe']['secret_key'])) {
                throw new \Exception('Stripe secret key is not configured');
            }

            Stripe::setApiKey($this->config['gateways']['stripe']['secret_key']);

            // Log payment attempt
            Log::info('Creating Stripe payment intent', [
                'payment_id' => $payment->id,
                'amount' => $payment->amount,
                'currency' => $this->config['gateways']['stripe']['currency'] ?? 'usd'
            ]);

            // Create payment intent
            $paymentIntent = PaymentIntent::create([
                'amount' => $this->convertToStripeAmount($payment->amount),
                'currency' => $this->config['gateways']['stripe']['currency'] ?? 'usd',
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

            // Update payment with Stripe data
            $payment->update([
                'payment_id' => $paymentIntent->id,
                'transaction_data' => [
                    'client_secret' => $paymentIntent->client_secret,
                    'status' => $paymentIntent->status,
                    'stripe_payment_intent_id' => $paymentIntent->id
                ]
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
                'error' => $e->getMessage(),
                'stripe_error' => $e->getStripeError()
            ]);

            return [
                'success' => false,
                'message' => 'Stripe payment failed: '
            ];
        } catch (\Exception $e) {
            Log::error('Stripe payment error', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Stripe payment failed: '
            ];
        }
    }

    /**
     * Process PayPal payment
     */
    protected function processPayPalPayment(Payment $payment, Request $request)
    {
        try {
            // Check if PayPal is configured
            if (empty($this->config['gateways']['paypal']['client_id']) || empty($this->config['gateways']['paypal']['client_secret'])) {
                throw new \Exception('PayPal credentials are not configured');
            }

            // Log payment attempt
            Log::info('Creating PayPal payment', [
                'payment_id' => $payment->id,
                'amount' => $payment->amount,
                'currency' => $this->config['gateways']['paypal']['currency'] ?? 'USD'
            ]);

            // Here you would implement PayPal payment creation
            // For now, return a placeholder response
            return [
                'success' => true,
                'data' => [
                    'redirect_url' => route('payment.paypal.redirect', $payment->id),
                    'payment_id' => 'PAYPAL-' . $payment->id
                ]
            ];
        } catch (\Exception $e) {
            Log::error('PayPal payment error', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'PayPal payment failed: '
            ];
        }
    }

    /**
     * Process PayMob payment
     */
    protected function processPayMobPayment(Payment $payment, Request $request)
    {
        try {
            // Check if PayMob is configured
            if (empty($this->config['gateways']['paymob']['api_key'])) {
                throw new \Exception('PayMob API key is not configured');
            }

            // Log payment attempt
            Log::info('Creating PayMob payment', [
                'payment_id' => $payment->id,
                'amount' => $payment->amount,
                'currency' => $this->config['gateways']['paymob']['currency'] ?? 'EGP'
            ]);

            // Here you would implement PayMob payment creation
            // For now, return a placeholder response
            return [
                'success' => true,
                'data' => [
                    'redirect_url' => route('payment.paymob.redirect', $payment->id),
                    'payment_id' => 'PAYMOB-' . $payment->id
                ]
            ];
        } catch (\Exception $e) {
            Log::error('PayMob payment error', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'PayMob payment failed: '
            ];
        }
    }

    /**
     * Confirm Stripe payment
     */
    public function confirmStripePayment(string $paymentIntentId)
    {
        try {
            if (empty($this->config['gateways']['stripe']['secret_key'])) {
                throw new \Exception('Stripe secret key is not configured');
            }

            Stripe::setApiKey($this->config['gateways']['stripe']['secret_key']);

            $paymentIntent = PaymentIntent::retrieve($paymentIntentId);

            if ($paymentIntent->status === 'succeeded') {
                return [
                    'success' => true,
                    'data' => [
                        'status' => $paymentIntent->status,
                        'amount' => $this->convertFromStripeAmount($paymentIntent->amount),
                        'currency' => $paymentIntent->currency
                    ]
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Payment not completed. Status: ' . $paymentIntent->status
                ];
            }
        } catch (ApiErrorException $e) {
            Log::error('Stripe confirmation API error', [
                'payment_intent_id' => $paymentIntentId,
                'error' => $e->getMessage(),
                'stripe_error' => $e->getStripeError()
            ]);

            return [
                'success' => false,
                'message' => 'Stripe confirmation failed: '
            ];
        } catch (\Exception $e) {
            Log::error('Stripe confirmation error', [
                'payment_intent_id' => $paymentIntentId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Stripe confirmation failed: '
            ];
        }
    }

    /**
     * Handle webhook from payment gateways
     */
    public function handleWebhook(Request $request, string $gateway)
    {
        try {
            Log::info("Webhook received from {$gateway}", [
                'payload' => $request->all(),
                'headers' => $request->headers->all()
            ]);

            switch ($gateway) {
                case 'stripe':
                    return $this->handleStripeWebhook($request);
                case 'paypal':
                    return $this->handlePayPalWebhook($request);
                case 'paymob':
                    return $this->handlePayMobWebhook($request);
                default:
                    Log::warning("Unknown gateway webhook: {$gateway}");
                    return false;
            }
        } catch (\Exception $e) {
            Log::error("Webhook handling error for {$gateway}: " , [
                'payload' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            return false;
        }
    }

    /**
     * Handle Stripe webhook
     */
    protected function handleStripeWebhook(Request $request)
    {
        try {
            $payload = $request->getContent();
            $sigHeader = $request->header('Stripe-Signature');
            $endpointSecret = $this->config['gateways']['stripe']['webhook_secret'] ?? null;

            if (!$endpointSecret) {
                Log::warning('Stripe webhook secret not configured');
                return false;
            }

            // Verify webhook signature
            $event = \Stripe\Webhook::constructEvent(
                $payload, $sigHeader, $endpointSecret
            );

            // Handle the event
            switch ($event->type) {
                case 'payment_intent.succeeded':
                    $this->handlePaymentSucceeded($event->data->object);
                    break;
                case 'payment_intent.payment_failed':
                    $this->handlePaymentFailed($event->data->object);
                    break;
                default:
                    Log::info('Unhandled Stripe event: ' . $event->type);
            }

            return true;
        } catch (\UnexpectedValueException $e) {
            Log::error('Invalid payload in Stripe webhook', [
                'error' => $e->getMessage()
            ]);
            return false;
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            Log::error('Invalid signature in Stripe webhook', [
                'error' => $e->getMessage()
            ]);
            return false;
        } catch (\Exception $e) {
            Log::error('Stripe webhook handling error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Handle PayPal webhook
     */
    protected function handlePayPalWebhook(Request $request)
    {
        try {
            // Implement PayPal webhook handling
            Log::info('PayPal webhook received', [
                'payload' => $request->all()
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('PayPal webhook handling error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Handle PayMob webhook
     */
    protected function handlePayMobWebhook(Request $request)
    {
        try {
            // Implement PayMob webhook handling
            Log::info('PayMob webhook received', [
                'payload' => $request->all()
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('PayMob webhook handling error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Handle successful payment
     */
    protected function handlePaymentSucceeded($paymentIntent)
    {
        try {
            $payment = Payment::where('payment_id', $paymentIntent->id)->first();

            if ($payment) {
                $payment->update([
                    'status' => 'completed',
                    'transaction_data' => array_merge($payment->transaction_data ?? [], [
                        'status' => $paymentIntent->status,
                        'completed_at' => now(),
                        'last_updated' => now()
                    ])
                ]);

                Log::info('Payment marked as completed', [
                    'payment_id' => $payment->id,
                    'gateway_payment_id' => $paymentIntent->id
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error handling payment succeeded', [
                'payment_intent_id' => $paymentIntent->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Handle failed payment
     */
    protected function handlePaymentFailed($paymentIntent)
    {
        try {
            $payment = Payment::where('payment_id', $paymentIntent->id)->first();

            if ($payment) {
                $payment->update([
                    'status' => 'failed',
                    'transaction_data' => array_merge($payment->transaction_data ?? [], [
                        'status' => $paymentIntent->status,
                        'failure_reason' => $paymentIntent->last_payment_error->message ?? 'Unknown error',
                        'last_updated' => now()
                    ])
                ]);

                Log::info('Payment marked as failed', [
                    'payment_id' => $payment->id,
                    'gateway_payment_id' => $paymentIntent->id
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error handling payment failed', [
                'payment_intent_id' => $paymentIntent->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Check if a gateway is enabled
     */
    protected function isGatewayEnabled(string $gateway): bool
    {
        try {
            return $this->config['gateways'][$gateway]['enabled'] ?? false;
        } catch (\Exception $e) {
            Log::error('Error checking gateway status', [
                'gateway' => $gateway,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Convert amount to Stripe format (cents)
     */
    protected function convertToStripeAmount(float $amount): int
    {
        try {
            return (int) ($amount * 100);
        } catch (\Exception $e) {
            Log::error('Error converting amount to Stripe format', [
                'amount' => $amount,
                'error' => $e->getMessage()
            ]);
            return 0;
        }
    }

    /**
     * Convert amount from Stripe format (cents to dollars)
     */
    protected function convertFromStripeAmount(int $amount): float
    {
        try {
            return $amount / 100;
        } catch (\Exception $e) {
            Log::error('Error converting amount from Stripe format', [
                'amount' => $amount,
                'error' => $e->getMessage()
            ]);
            return 0.0;
        }
    }

    /**
     * Get payment status
     */
    public function getPaymentStatus(Payment $payment)
    {
        try {
            if ($payment->gateway_payment_id) {
                switch ($payment->gateway) {
                    case 'stripe':
                        return $this->getStripePaymentStatus($payment);
                    case 'paypal':
                        return $this->getPayPalPaymentStatus($payment);
                    case 'paymob':
                        return $this->getPayMobPaymentStatus($payment);
                    default:
                        return $payment->status;
                }
            }

            return $payment->status;
        } catch (\Exception $e) {
            Log::error('Error getting payment status', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $payment->status;
        }
    }

    /**
     * Get Stripe payment status
     */
    protected function getStripePaymentStatus(Payment $payment)
    {
        try {
            if (empty($this->config['gateways']['stripe']['secret_key'])) {
                return $payment->status;
            }

            Stripe::setApiKey($this->config['gateways']['stripe']['secret_key']);
            $paymentIntent = PaymentIntent::retrieve($payment->payment_id);

            return $paymentIntent->status;
        } catch (\Exception $e) {
            Log::error('Error getting Stripe payment status', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage()
            ]);

            return $payment->status;
        }
    }

    /**
     * Get PayPal payment status
     */
    protected function getPayPalPaymentStatus(Payment $payment)
    {
        try {
            // Implement PayPal payment status check
            return $payment->status;
        } catch (\Exception $e) {
            Log::error('Error getting PayPal payment status', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage()
            ]);

            return $payment->status;
        }
    }

    /**
     * Get PayMob payment status
     */
    protected function getPayMobPaymentStatus(Payment $payment)
    {
        try {
            // Implement PayMob payment status check
            return $payment->status;
        } catch (\Exception $e) {
            Log::error('Error getting PayMob payment status', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage()
            ]);

            return $payment->status;
        }
    }
}
