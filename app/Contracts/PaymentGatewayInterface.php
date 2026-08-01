<?php

namespace App\Contracts;

use App\Models\Order;
use Illuminate\Http\Request;

interface PaymentGatewayInterface
{
    /**
     * Charge an order and return gateway response (e.g. redirect URL, form payload, status).
     *
     * @param Order $order
     * @return array
     */
    public function charge(Order $order): array;

    /**
     * Verify payment status using response payload.
     *
     * @param array $payload
     * @return bool
     */
    public function verify(array $payload): bool;

    /**
     * Handle incoming payment gateway webhooks.
     *
     * @param Request $request
     * @return array
     */
    public function handleWebhook(Request $request): array;
}
