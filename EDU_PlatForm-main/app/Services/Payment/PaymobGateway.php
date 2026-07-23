<?php

namespace App\Services\Payment;

use App\Contracts\PaymentGatewayInterface;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class PaymobGateway implements PaymentGatewayInterface
{
    protected $apiKey;
    protected $integrationId;
    protected $iframeId;
    protected $hmacSecret;
    protected $currency;
    protected $baseUrl = 'https://accept.paymob.com/api';

    public function __construct()
    {
        $this->apiKey = config('payment.gateways.paymob.api_key');
        $this->integrationId = config('payment.gateways.paymob.integration_id');
        $this->iframeId = config('payment.gateways.paymob.iframe_id');
        $this->hmacSecret = config('payment.gateways.paymob.hmac_secret');
        $this->currency = config('payment.gateways.paymob.currency', 'EGP');
    }

    /**
     * @inheritDoc
     */
    public function initiatePayment(Payment $payment, Request $request): array
    {
        try {
            if (empty($this->apiKey) || empty($this->integrationId) || empty($this->iframeId)) {
                throw new Exception('Paymob credentials are not properly configured.');
            }

            Log::info('Initiating Paymob payment process', [
                'payment_id' => $payment->id,
                'amount' => $payment->amount
            ]);

            // Step 1: Authentication
            $authToken = $this->getAuthToken();

            // Step 2: Order Registration
            $paymobOrderId = $this->registerOrder($authToken, $payment);

            // Step 3: Payment Key Generation
            $paymentToken = $this->getPaymentKey($authToken, $paymobOrderId, $payment);

            // Save details to transaction_data
            $payment->update([
                'payment_id' => $paymobOrderId,
                'transaction_data' => array_merge($payment->transaction_data ?? [], [
                    'paymob_order_id' => $paymobOrderId,
                    'payment_key' => $paymentToken,
                    'last_updated' => now()
                ])
            ]);

            $redirectUrl = "https://accept.paymob.com/api/acceptance/iframes/{$this->iframeId}?payment_token={$paymentToken}";

            return [
                'success' => true,
                'data' => [
                    'redirect_url' => $redirectUrl,
                    'payment_id' => $paymobOrderId
                ]
            ];
        } catch (Exception $e) {
            Log::error('Paymob initiation failed', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Step 1: Get authentication token using API key.
     */
    protected function getAuthToken(): string
    {
        $response = Http::post("{$this->baseUrl}/auth/tokens", [
            'api_key' => $this->apiKey
        ]);

        if ($response->failed()) {
            Log::error('Paymob authentication token request failed', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            throw new Exception('Paymob API authentication failed.');
        }

        return $response->json('token');
    }

    /**
     * Step 2: Register order in Paymob system.
     */
    protected function registerOrder(string $authToken, Payment $payment): string
    {
        $amountCents = (int) round($payment->amount * 100);

        // We append the payment ID to keep a unique order reference
        $merchantOrderId = 'EDU_PLAT_' . $payment->id . '_' . time();

        $response = Http::post("{$this->baseUrl}/ecommerce/orders", [
            'auth_token' => $authToken,
            'delivery_needed' => 'false',
            'amount_cents' => (string) $amountCents,
            'currency' => $this->currency,
            'merchant_order_id' => $merchantOrderId,
        ]);

        if ($response->failed()) {
            Log::error('Paymob order registration failed', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            throw new Exception('Paymob order registration failed.');
        }

        return (string) $response->json('id');
    }

    /**
     * Step 3: Generate the single-use payment key.
     */
    protected function getPaymentKey(string $authToken, string $paymobOrderId, Payment $payment): string
    {
        $amountCents = (int) round($payment->amount * 100);
        $user = $payment->user;

        // Parse user names
        $nameParts = explode(' ', trim($user->name));
        $firstName = $nameParts[0] ?? 'Student';
        $lastName = isset($nameParts[1]) ? implode(' ', array_slice($nameParts, 1)) : 'User';
        if (empty($lastName)) {
            $lastName = 'User';
        }

        // billing data (all fields are required by Paymob acceptance API)
        $billingData = [
            'apartment' => 'NA',
            'email' => $user->email,
            'floor' => 'NA',
            'first_name' => $firstName,
            'street' => 'NA',
            'building' => 'NA',
            'phone_number' => $user->phone ?? '+201000000000',
            'shipping_method' => 'PKG',
            'postal_code' => 'NA',
            'city' => 'Cairo',
            'country' => 'EG',
            'last_name' => $lastName,
            'state' => 'Cairo'
        ];

        $response = Http::post("{$this->baseUrl}/acceptance/payment_keys", [
            'auth_token' => $authToken,
            'amount_cents' => (string) $amountCents,
            'expiration' => 3600,
            'order_id' => $paymobOrderId,
            'billing_data' => $billingData,
            'currency' => $this->currency,
            'integration_id' => (int) $this->integrationId,
            'lock_order_to_token' => true
        ]);

        if ($response->failed()) {
            Log::error('Paymob payment key generation failed', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            throw new Exception('Paymob payment key generation failed.');
        }

        return $response->json('token');
    }

    /**
     * @inheritDoc
     */
    public function handleCallback(Request $request): array
    {
        Log::info('Paymob redirect callback received', $request->all());

        // Validate GET HMAC
        if (!$this->verifyCallbackHmac($request)) {
            Log::warning('Paymob GET callback HMAC verification failed');
            return [
                'success' => false,
                'message' => 'Security signature verification failed.'
            ];
        }

        $success = $request->query('success') === 'true';
        $paymobOrderId = $request->query('order');
        $transactionId = $request->query('id');

        if ($success) {
            return [
                'success' => true,
                'payment_id' => $paymobOrderId,
                'transaction_data' => [
                    'paymob_transaction_id' => $transactionId,
                    'payment_status' => 'succeeded',
                    'verified_via' => 'callback'
                ]
            ];
        }

        return [
            'success' => false,
            'payment_id' => $paymobOrderId,
            'message' => 'Transaction was not successful.'
        ];
    }

    /**
     * @inheritDoc
     */
    public function handleWebhook(Request $request): array
    {
        Log::info('Paymob webhook received');

        // Validate POST HMAC
        if (!$this->verifyWebhookHmac($request)) {
            Log::warning('Paymob POST webhook HMAC verification failed');
            return [
                'success' => false,
                'message' => 'Security HMAC verification failed.'
            ];
        }

        $payload = $request->all();
        $type = $payload['type'] ?? '';

        if ($type !== 'TRANSACTION') {
            return [
                'success' => false,
                'message' => 'Unsupported webhook transaction type.'
            ];
        }

        $data = $payload['obj'] ?? [];
        $paymobOrderId = $data['order']['id'] ?? ($data['order'] ?? null);
        $transactionId = $data['id'] ?? null;
        $success = isset($data['success']) && ($data['success'] === true || $data['success'] === 'true' || $data['success'] === 1 || $data['success'] === '1');

        if (!$paymobOrderId) {
            return [
                'success' => false,
                'message' => 'Missing Paymob order ID in webhook payload.'
            ];
        }

        if ($success) {
            return [
                'success' => true,
                'payment_id' => (string) $paymobOrderId,
                'transaction_data' => [
                    'paymob_transaction_id' => $transactionId,
                    'payment_status' => 'succeeded',
                    'verified_via' => 'webhook'
                ]
            ];
        }

        return [
            'success' => false,
            'payment_id' => (string) $paymobOrderId,
            'message' => 'Transaction marked as failed.'
        ];
    }

    /**
     * Verify GET callback HMAC.
     */
    protected function verifyCallbackHmac(Request $request): bool
    {
        $hmac = $request->query('hmac');
        if (empty($hmac)) {
            return false;
        }

        // Keys concatenated in alphabetical order for GET callback
        $keys = [
            'amount_cents',
            'created_at',
            'currency',
            'error_occured',
            'has_parent_transaction',
            'id',
            'integration_id',
            'is_3d_secure',
            'is_auth',
            'is_capture',
            'is_voided',
            'is_refunded',
            'owner',
            'pending',
            'source_data_pan',
            'source_data_sub_type',
            'source_data_type',
            'success'
        ];

        $string = '';
        foreach ($keys as $key) {
            // Paymob flat key mappings on redirect
            $val = $request->query($key);
            if ($val === null) {
                continue;
            }
            if ($val === true || $val === 'true') {
                $string .= 'true';
            } elseif ($val === false || $val === 'false') {
                $string .= 'false';
            } else {
                $string .= $val;
            }
        }

        $calculatedHmac = hash_hmac('sha512', $string, $this->hmacSecret);

        return hash_equals($calculatedHmac, $hmac);
    }

    /**
     * Verify POST webhook HMAC.
     */
    protected function verifyWebhookHmac(Request $request): bool
    {
        $hmac = $request->query('hmac') ?? $request->input('hmac');
        if (empty($hmac)) {
            return false;
        }

        $data = $request->input('obj');
        if (empty($data)) {
            return false;
        }

        // Paymob POST body HMAC signature verification fields structure
        $string = 
            ($data['amount_cents'] ?? '') .
            ($data['created_at'] ?? '') .
            ($data['currency'] ?? '') .
            (($data['error_occured'] ?? false) === true || ($data['error_occured'] ?? false) === 'true' ? 'true' : 'false') .
            (($data['has_parent_transaction'] ?? false) === true || ($data['has_parent_transaction'] ?? false) === 'true' ? 'true' : 'false') .
            ($data['id'] ?? '') .
            ($data['integration_id'] ?? '') .
            (($data['is_3d_secure'] ?? false) === true || ($data['is_3d_secure'] ?? false) === 'true' ? 'true' : 'false') .
            (($data['is_auth'] ?? false) === true || ($data['is_auth'] ?? false) === 'true' ? 'true' : 'false') .
            (($data['is_capture'] ?? false) === true || ($data['is_capture'] ?? false) === 'true' ? 'true' : 'false') .
            (($data['is_voided'] ?? false) === true || ($data['is_voided'] ?? false) === 'true' ? 'true' : 'false') .
            (($data['is_refunded'] ?? false) === true || ($data['is_refunded'] ?? false) === 'true' ? 'true' : 'false') .
            ($data['owner'] ?? '') .
            (($data['pending'] ?? false) === true || ($data['pending'] ?? false) === 'true' ? 'true' : 'false') .
            ($data['source_data']['pan'] ?? ($data['source_data_pan'] ?? '')) .
            ($data['source_data']['sub_type'] ?? ($data['source_data_sub_type'] ?? '')) .
            ($data['source_data']['type'] ?? ($data['source_data_type'] ?? '')) .
            (($data['success'] ?? false) === true || ($data['success'] ?? false) === 'true' ? 'true' : 'false');

        $calculatedHmac = hash_hmac('sha512', $string, $this->hmacSecret);

        return hash_equals($calculatedHmac, $hmac);
    }
}
