<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment System Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">
                            <i class="fas fa-credit-card me-2"></i>
                            Payment System Test
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            This page tests the payment system configuration. Make sure you have set up your environment variables.
                        </div>

                        <h5>Configuration Status</h5>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="card mb-3">
                                    <div class="card-body text-center">
                                        <i class="fas fa-credit-card fa-2x text-primary mb-2"></i>
                                        <h6>Stripe</h6>
                                        <small class="text-muted">
                                            @if(config('payment.gateways.stripe.enabled'))
                                                <span class="badge bg-success">Enabled</span>
                                            @else
                                                <span class="badge bg-danger">Disabled</span>
                                            @endif
                                        </small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card mb-3">
                                    <div class="card-body text-center">
                                        <i class="fab fa-paypal fa-2x text-primary mb-2"></i>
                                        <h6>PayPal</h6>
                                        <small class="text-muted">
                                            @if(config('payment.gateways.paypal.enabled'))
                                                <span class="badge bg-success">Enabled</span>
                                            @else
                                                <span class="badge bg-danger">Disabled</span>
                                            @endif
                                        </small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card mb-3">
                                    <div class="card-body text-center">
                                        <i class="fas fa-mobile-alt fa-2x text-primary mb-2"></i>
                                        <h6>PayMob</h6>
                                        <small class="text-muted">
                                            @if(config('payment.gateways.paymob.enabled'))
                                                <span class="badge bg-success">Enabled</span>
                                            @else
                                                <span class="badge bg-danger">Disabled</span>
                                            @endif
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <h5>Environment Variables</h5>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Variable</th>
                                        <th>Status</th>
                                        <th>Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>STRIPE_PUBLIC_KEY</td>
                                        <td>
                                            @if(env('STRIPE_PUBLIC_KEY'))
                                                <span class="badge bg-success">Set</span>
                                            @else
                                                <span class="badge bg-danger">Missing</span>
                                            @endif
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                {{ env('STRIPE_PUBLIC_KEY') ? substr(env('STRIPE_PUBLIC_KEY'), 0, 20) . '...' : 'Not set' }}
                                            </small>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>STRIPE_SECRET_KEY</td>
                                        <td>
                                            @if(env('STRIPE_SECRET_KEY'))
                                                <span class="badge bg-success">Set</span>
                                            @else
                                                <span class="badge bg-danger">Missing</span>
                                            @endif
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                {{ env('STRIPE_SECRET_KEY') ? 'sk_***' . substr(env('STRIPE_SECRET_KEY'), -4) : 'Not set' }}
                                            </small>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>PAYPAL_CLIENT_ID</td>
                                        <td>
                                            @if(env('PAYPAL_CLIENT_ID'))
                                                <span class="badge bg-success">Set</span>
                                            @else
                                                <span class="badge bg-danger">Missing</span>
                                            @endif
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                {{ env('PAYPAL_CLIENT_ID') ? substr(env('PAYPAL_CLIENT_ID'), 0, 20) . '...' : 'Not set' }}
                                            </small>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>PAYMOB_API_KEY</td>
                                        <td>
                                            @if(env('PAYMOB_API_KEY'))
                                                <span class="badge bg-success">Set</span>
                                            @else
                                                <span class="badge bg-danger">Missing</span>
                                            @endif
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                {{ env('PAYMOB_API_KEY') ? substr(env('PAYMOB_API_KEY'), 0, 20) . '...' : 'Not set' }}
                                            </small>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <h5>Test Actions</h5>
                        <div class="d-grid gap-2 d-md-block">
                            <a href="{{ route('payment.checkout', 1) }}" class="btn btn-primary">
                                <i class="fas fa-shopping-cart me-2"></i>
                                Test Checkout (Course ID: 1)
                            </a>
                            <a href="{{ route('home') }}" class="btn btn-secondary">
                                <i class="fas fa-home me-2"></i>
                                Back to Home
                            </a>
                        </div>

                        <h5 class="mt-4">Debug Information</h5>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <tbody>
                                    <tr>
                                        <td><strong>Payment Config Loaded:</strong></td>
                                        <td>
                                            @if(config('payment'))
                                                <span class="badge bg-success">Yes</span>
                                            @else
                                                <span class="badge bg-danger">No</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Stripe Config:</strong></td>
                                        <td>
                                            @if(config('payment.gateways.stripe'))
                                                <span class="badge bg-success">Loaded</span>
                                            @else
                                                <span class="badge bg-danger">Not Loaded</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Stripe Secret Key Length:</strong></td>
                                        <td>
                                            @if(env('STRIPE_SECRET_KEY'))
                                                {{ strlen(env('STRIPE_SECRET_KEY')) }} characters
                                            @else
                                                <span class="badge bg-danger">Not Set</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Stripe Public Key Length:</strong></td>
                                        <td>
                                            @if(env('STRIPE_PUBLIC_KEY'))
                                                {{ strlen(env('STRIPE_PUBLIC_KEY')) }} characters
                                            @else
                                                <span class="badge bg-danger">Not Set</span>
                                            @endif
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <hr>

                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Important:</strong> This test page should be removed in production.
                            Make sure to set up your payment gateway credentials in the `.env` file.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
