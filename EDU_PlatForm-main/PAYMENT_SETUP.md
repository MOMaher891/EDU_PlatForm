# Payment Gateway Setup Guide

This guide will help you set up the payment gateways for your LMS platform.

## Prerequisites

- Laravel 12.x
- Composer
- Stripe, PayPal, and/or PayMob accounts

## Installation

The payment gateway packages have already been installed:

```bash
composer require stripe/stripe-php laravel/cashier
```

## Environment Configuration

Add the following variables to your `.env` file:

### Payment Gateway Settings
```env
# Payment Gateway Configuration
PAYMENT_DEFAULT=stripe

# Stripe Configuration
STRIPE_ENABLED=true
STRIPE_PUBLIC_KEY=pk_test_your_stripe_public_key_here
STRIPE_SECRET_KEY=sk_test_your_stripe_secret_key_here
STRIPE_WEBHOOK_SECRET=whsec_your_stripe_webhook_secret_here
STRIPE_CURRENCY=usd
STRIPE_MIN_AMOUNT=0.50

# PayPal Configuration
PAYPAL_ENABLED=true
PAYPAL_CLIENT_ID=your_paypal_client_id_here
PAYPAL_CLIENT_SECRET=your_paypal_client_secret_here
PAYPAL_MODE=sandbox
PAYPAL_CURRENCY=USD
PAYPAL_WEBHOOK_ID=your_paypal_webhook_id_here

# PayMob Configuration
PAYMOB_ENABLED=true
PAYMOB_API_KEY=your_paymob_api_key_here
PAYMOB_INTEGRATION_ID=your_paymob_integration_id_here
PAYMOB_IFRAME_ID=your_paymob_iframe_id_here
PAYMOB_HMAC_SECRET=your_paymob_hmac_secret_here
PAYMOB_CURRENCY=EGP

# Payment Settings
PAYMENT_TAX_ENABLED=true
PAYMENT_TAX_RATE=15
PAYMENT_WEBHOOKS_ENABLED=true
PAYMENT_WEBHOOK_TIMEOUT=30
PAYMENT_WEBHOOK_RETRY_ATTEMPTS=3
PAYMENT_FRAUD_DETECTION=true
PAYMENT_MAX_AMOUNT=10000
PAYMENT_RATE_LIMITING=true
PAYMENT_EMAIL_SUCCESS=true
PAYMENT_EMAIL_FAILED=true
PAYMENT_EMAIL_REFUNDED=true
```

## Stripe Setup

1. **Create Stripe Account**
   - Go to [stripe.com](https://stripe.com) and create an account
   - Complete your business profile

2. **Get API Keys**
   - Go to Developers > API keys in your Stripe dashboard
   - Copy your publishable key and secret key
   - Update your `.env` file

3. **Set Webhook Endpoint**
   - Go to Developers > Webhooks in your Stripe dashboard
   - Add endpoint: `https://yourdomain.com/payment/webhook/stripe`
   - Select events: `payment_intent.succeeded`, `payment_intent.payment_failed`
   - Copy the webhook secret and update your `.env` file

## PayPal Setup

1. **Create PayPal Developer Account**
   - Go to [developer.paypal.com](https://developer.paypal.com)
   - Create a developer account

2. **Create App**
   - Go to My Apps & Credentials
   - Create a new app
   - Copy the client ID and secret
   - Update your `.env` file

3. **Set Webhook Endpoint**
   - Go to Webhooks in your PayPal app
   - Add endpoint: `https://yourdomain.com/payment/webhook/paypal`
   - Select events: `PAYMENT.CAPTURE.COMPLETED`, `PAYMENT.CAPTURE.DENIED`

## PayMob Setup

1. **Create PayMob Account**
   - Go to [paymob.com](https://paymob.com) and create an account
   - Complete your business profile

2. **Get API Credentials**
   - Go to your PayMob dashboard
   - Copy your API key, integration ID, and iframe ID
   - Update your `.env` file

3. **Set Webhook Endpoint**
   - Configure webhook URL: `https://yourdomain.com/payment/webhook/paymob`

## Database Migration

Run the Cashier migrations:

```bash
php artisan migrate
```

## Testing

### Test Cards (Stripe)
- **Success**: 4242 4242 4242 4242
- **Decline**: 4000 0000 0000 0002
- **3D Secure**: 4000 0025 0000 3155

### Test PayPal
- Use PayPal sandbox accounts for testing

## Features

### ✅ Implemented
- Stripe payment processing with Elements
- PayPal integration
- PayMob integration
- Webhook handling
- Payment confirmation
- Course enrollment after payment
- Tax calculation
- Multi-currency support
- Security features (fraud detection, rate limiting)

### 🔄 Payment Flow
1. Student selects course/section
2. Student chooses payment method
3. Payment is processed through selected gateway
4. Payment is confirmed
5. Student is enrolled in course
6. Access is granted to course content

### 🛡️ Security Features
- CSRF protection
- Webhook signature verification
- Rate limiting
- Fraud detection
- Secure payment processing
- SSL encryption

## Troubleshooting

### Common Issues

1. **Stripe Elements not loading**
   - Check if Stripe public key is correct
   - Ensure JavaScript is enabled
   - Check browser console for errors

2. **Webhook not working**
   - Verify webhook URL is accessible
   - Check webhook secret is correct
   - Ensure SSL certificate is valid

3. **Payment not completing**
   - Check payment gateway logs
   - Verify API keys are correct
   - Check if gateway is enabled in config

### Debug Mode

Enable debug mode in your `.env`:

```env
APP_DEBUG=true
LOG_LEVEL=debug
```

Check logs in `storage/logs/laravel.log`

## Support

For payment gateway specific issues:
- **Stripe**: [support.stripe.com](https://support.stripe.com)
- **PayPal**: [developer.paypal.com/support](https://developer.paypal.com/support)
- **PayMob**: [paymob.com/support](https://paymob.com/support)

## License

This payment integration is part of the LMS Platform and follows the same license terms.
