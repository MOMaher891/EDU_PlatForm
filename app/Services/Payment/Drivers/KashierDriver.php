<?php

namespace App\Services\Payment\Drivers;

use App\Contracts\PaymentGatewayInterface;
use App\Models\Order;
use App\Models\PaymentGateway;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class KashierDriver implements PaymentGatewayInterface
{
    protected string $merchantId;
    protected string $apiKey;
    protected string $secretKey;
    protected string $mode;
    protected string $currency;
    protected string $baseUrl;
    protected string $apiUrl;

    public function __construct(?array $credentials = null)
    {
        if ($credentials) {
            $this->merchantId = (string) ($credentials['merchant_id'] ?? '');
            $this->apiKey = (string) ($credentials['api_key'] ?? '');
            $this->secretKey = (string) ($credentials['secret_key'] ?? '');
            $this->mode = (string) ($credentials['mode'] ?? 'sandbox');
            $this->currency = (string) ($credentials['currency'] ?? 'EGP');
        } else {
            // 1. Dynamically load from payment_gateways DB table first
            $gateway = PaymentGateway::where('code', 'kashier')->where('is_active', true)->first()
                ?? PaymentGateway::where('code', 'kashier')->first();

            if ($gateway && !empty($gateway->credentials)) {
                $creds = $gateway->credentials;
                $this->merchantId = (string) ($creds['merchant_id'] ?? config('payment.gateways.kashier.merchant_id', ''));
                $this->apiKey = (string) ($creds['api_key'] ?? config('payment.gateways.kashier.api_key', ''));
                $this->secretKey = (string) ($creds['secret_key'] ?? config('payment.gateways.kashier.secret_key', ''));
                $this->mode = (string) ($gateway->mode ?? config('payment.gateways.kashier.mode', 'sandbox'));
                $this->currency = (string) ($creds['currency'] ?? config('payment.gateways.kashier.currency', 'EGP'));
            } else {
                // Fall back to config/payment.php if needed
                $this->merchantId = (string) config('payment.gateways.kashier.merchant_id', '');
                $this->apiKey = (string) config('payment.gateways.kashier.api_key', '');
                $this->secretKey = (string) config('payment.gateways.kashier.secret_key', '');
                $this->mode = (string) config('payment.gateways.kashier.mode', 'sandbox');
                $this->currency = (string) config('payment.gateways.kashier.currency', 'EGP');
            }
        }

        // 2. Respect mode column (sandbox vs live) to route endpoints
        $this->baseUrl = 'https://checkout.kashier.io';
        $this->apiUrl = strtolower($this->mode) === 'live'
            ? 'https://api.kashier.io'
            : 'https://test-api.kashier.io';
    }

    /**
     * Get configured Merchant ID.
     */
    public function getMerchantId(): string
    {
        return $this->merchantId;
    }

    /**
     * Get configured Secret Key.
     */
    public function getSecretKey(): string
    {
        return $this->secretKey;
    }

    /**
     * Get configured Mode.
     */
    public function getMode(): string
    {
        return $this->mode;
    }

    /**
     * Calculate server-side HMAC-SHA256 signature.
     * Formula: "/?payment=" . MerchantID . "." . OrderID . "." . Amount . "." . Currency
     *
     * @param string $orderId
     * @param string|float $amount
     * @param string $currency
     * @return string
     */
    public function generateHash(string $orderId, string|float $amount, string $currency): string
    {
        $formattedAmount = number_format((float) $amount, 2, '.', '');
        $path = "/?payment={$this->merchantId}.{$orderId}.{$formattedAmount}.{$currency}";
        return hash_hmac('sha256', $path, $this->secretKey);
    }

    /**
     * Validate payload/header signature against calculated HMAC SHA256 signature.
     *
     * @param array $payload
     * @param string|null $signatureHeader
     * @return bool
     */
    public function verifySignature(array $payload, ?string $signatureHeader = null): bool
    {
        $sig = !empty($signatureHeader)
            ? trim($signatureHeader)
            : ($payload['signature'] ?? $payload['queryString'] ?? $payload['data']['signature'] ?? null);

        if (empty($sig)) {
            return false;
        }

        $orderId = $payload['data']['merchantOrderId'] ?? $payload['merchantOrderId'] ?? $payload['orderId'] ?? null;
        $amount = $payload['data']['amount'] ?? $payload['amount'] ?? null;
        $currency = $payload['data']['currency'] ?? $payload['currency'] ?? $this->currency;

        if (!$orderId || !$amount) {
            return false;
        }

        $expected = $this->generateHash((string) $orderId, $amount, (string) $currency);
        return hash_equals($expected, trim($sig));
    }

    /**
     * Charge an order using Kashier driver.
     *
     * @param Order $order
     * @return array
     */
    public function charge(Order $order): array
    {
        try {
            $amount = number_format((float) $order->total_amount, 2, '.', '');
            $currency = $order->currency ?? $this->currency;
            $orderId = (string) $order->order_number;

            $hash = $this->generateHash($orderId, $amount, $currency);
            $modeParam = strtolower($this->mode) === 'live' ? 'live' : 'test';
            $merchantRedirect = route('payment.success.order', ['order' => $orderId]);
            $encodedRedirect = urlencode($merchantRedirect);

            $checkoutUrl = "{$this->baseUrl}/?merchantId={$this->merchantId}&orderId={$orderId}&amount={$amount}&currency={$currency}&hash={$hash}&mode={$modeParam}&merchantRedirect={$encodedRedirect}";

            $transaction = Transaction::where('order_id', $order->id)
                ->where('gateway_code', 'kashier')
                ->where('status', 'PENDING')
                ->where('created_at', '>=', now()->subMinutes(3))
                ->latest()
                ->first();

            if (!$transaction) {
                $transaction = Transaction::create([
                    'user_id' => $order->user_id,
                    'order_id' => $order->id,
                    'gateway_code' => 'kashier',
                    'gateway_transaction_id' => null,
                    'amount' => $order->total_amount,
                    'currency' => $currency,
                    'status' => 'PENDING',
                    'payment_method' => 'card',
                    'payable_type' => $order->payable_type,
                    'payable_id' => $order->payable_id,
                    'gateway_response' => [
                        'hash' => $hash,
                        'checkout_url' => $checkoutUrl,
                        'mode' => $this->mode,
                    ],
                ]);
            }

            return [
                'success' => true,
                'redirect_url' => $checkoutUrl,
                'hash' => $hash,
                'merchant_id' => $this->merchantId,
                'order_id' => $orderId,
                'merchant_redirect' => $merchantRedirect,
                'amount' => $amount,
                'currency' => $currency,
                'mode' => strtolower($this->mode) === 'live' ? 'live' : 'test',
                'transaction_id' => $transaction->id,
            ];
        } catch (\Exception $e) {
            Log::error('Kashier charge generation failed: ' . $e->getMessage(), [
                'order_id' => $order->id ?? null,
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'message' => 'Failed to initiate Kashier payment: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Verify callback signature / payload from Kashier.
     *
     * @param array $payload
     * @return bool
     */
    public function verify(array $payload): bool
    {
        return $this->verifySignature($payload);
    }

    /**
     * Handle incoming Kashier webhook notification.
     *
     * @param Request $request
     * @return array
     */
    public function handleWebhook(Request $request): array
    {
        $payload = $request->all();
        $signatureHeader = $request->header('x-kashier-signature');

        Log::info('Kashier webhook payload received', [
            'payload' => $payload,
            'signature_header' => $signatureHeader,
        ]);

        $isValid = $this->verifySignature($payload, $signatureHeader);
        $orderId = $payload['data']['merchantOrderId'] ?? $payload['merchantOrderId'] ?? $payload['orderId'] ?? null;
        $kashierTxId = $payload['data']['kashierOrderNumber'] ?? $payload['kashierOrderNumber'] ?? $payload['transactionId'] ?? null;

        if (!$isValid) {
            Log::warning('Kashier webhook signature verification failed');
            return [
                'success' => false,
                'message' => 'Invalid Kashier signature or failed payment status.',
            ];
        }

        return [
            'success' => true,
            'message' => 'Webhook signature verified successfully.',
            'order_id' => $orderId,
            'kashier_transaction_id' => $kashierTxId,
        ];
    }
}
