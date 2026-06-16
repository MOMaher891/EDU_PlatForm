<?php

namespace App\Services\Payment;

use App\Contracts\PaymentGatewayInterface;
use InvalidArgumentException;

class PaymentFactory
{
    /**
     * Resolve a payment gateway strategy.
     *
     * @param string $gateway
     * @return PaymentGatewayInterface
     * @throws InvalidArgumentException
     */
    public function make(string $gateway): PaymentGatewayInterface
    {
        switch (strtolower($gateway)) {
            case 'paymob':
                return app(PaymobGateway::class);
            case 'stripe':
                return app(StripeGateway::class);
            case 'paypal':
                return app(PayPalGateway::class);
            default:
                throw new InvalidArgumentException("Payment gateway [{$gateway}] is not supported.");
        }
    }
}
