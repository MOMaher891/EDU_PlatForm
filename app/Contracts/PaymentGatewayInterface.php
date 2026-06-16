<?php

namespace App\Contracts;

use App\Models\Payment;
use Illuminate\Http\Request;

interface PaymentGatewayInterface
{
    /**
     * Initiate payment and return response data (redirect URL or payload).
     *
     * @param Payment $payment
     * @param Request $request
     * @return array
     */
    public function initiatePayment(Payment $payment, Request $request): array;

    /**
     * Handle the redirect callback from the payment gateway.
     *
     * @param Request $request
     * @return array
     */
    public function handleCallback(Request $request): array;

    /**
     * Process the webhook (IPN) from the payment gateway.
     *
     * @param Request $request
     * @return array
     */
    public function handleWebhook(Request $request): array;
}
