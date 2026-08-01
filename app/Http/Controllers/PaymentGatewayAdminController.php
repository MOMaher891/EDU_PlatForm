<?php

namespace App\Http\Controllers;

use App\Models\PaymentGateway;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentGatewayAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    /**
     * Display listing of payment gateways.
     * Auto-seeds default gateways if database table is empty.
     *
     * @return View
     */
    public function index(): View
    {
        $this->ensureDefaultGatewaysExist();

        $gateways = PaymentGateway::orderBy('sort_order', 'asc')->get();

        return view('admin.payment_gateways.index', compact('gateways'));
    }

    /**
     * Update payment gateway credentials, status, mode, and sort order.
     *
     * @param Request $request
     * @param PaymentGateway $gateway
     * @return RedirectResponse
     */
    public function update(Request $request, PaymentGateway $gateway): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'mode' => 'required|in:sandbox,live',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'required|integer|min:0',
            'credentials' => 'required|array',
        ]);

        $credentials = $request->input('credentials', []);
        
        // Preserve existing credentials if empty masked inputs were submitted
        $existingCreds = $gateway->credentials ?? [];
        foreach ($credentials as $key => $val) {
            if (is_null($val) || $val === '') {
                if (isset($existingCreds[$key])) {
                    $credentials[$key] = $existingCreds[$key];
                }
            }
        }

        $gateway->update([
            'name' => $validated['name'],
            'mode' => $validated['mode'],
            'is_active' => $request->has('is_active') ? (bool) $request->input('is_active') : false,
            'sort_order' => (int) $validated['sort_order'],
            'credentials' => $credentials,
        ]);

        return redirect()->route('admin.payment-gateways.index')
            ->with('success', "تم تحديث إعدادات بوابة {$gateway->name} بنجاح.");
    }

    /**
     * Toggle gateway active status via AJAX.
     *
     * @param Request $request
     * @param PaymentGateway $gateway
     * @return JsonResponse
     */
    public function toggleStatus(Request $request, PaymentGateway $gateway): JsonResponse
    {
        $request->validate([
            'is_active' => 'required|boolean',
        ]);

        $gateway->update([
            'is_active' => (bool) $request->input('is_active'),
        ]);

        $statusText = $gateway->is_active ? 'مفعلة' : 'غير مفعلة';

        return response()->json([
            'success' => true,
            'message' => "أصبحت بوابة {$gateway->name} {$statusText} الآن.",
            'is_active' => $gateway->is_active,
        ]);
    }

    /**
     * Toggle gateway mode (sandbox vs live) via AJAX.
     *
     * @param Request $request
     * @param PaymentGateway $gateway
     * @return JsonResponse
     */
    public function toggleMode(Request $request, PaymentGateway $gateway): JsonResponse
    {
        $request->validate([
            'mode' => 'required|in:sandbox,live',
        ]);

        $gateway->update([
            'mode' => $request->input('mode'),
        ]);

        $modeText = $gateway->mode === 'live' ? 'الحي (Live)' : 'الاختباري (Sandbox)';

        return response()->json([
            'success' => true,
            'message' => "تم تغيير وضع بوابة {$gateway->name} إلى الوضع {$modeText}.",
            'mode' => $gateway->mode,
        ]);
    }

    /**
     * Update sort orders via AJAX.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function updateOrder(Request $request): JsonResponse
    {
        $request->validate([
            'orders' => 'required|array',
            'orders.*.id' => 'required|exists:payment_gateways,id',
            'orders.*.sort_order' => 'required|integer|min:0',
        ]);

        foreach ($request->input('orders') as $item) {
            PaymentGateway::where('id', $item['id'])->update([
                'sort_order' => (int) $item['sort_order'],
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'تم التحديث الفوري لترتيب بوابات الدفع بنجاح.',
        ]);
    }

    /**
     * Auto-seed default gateways if none exist.
     */
    protected function ensureDefaultGatewaysExist(): void
    {
        $defaultGateways = [
            [
                'code' => 'kashier',
                'name' => 'Kashier',
                'is_active' => true,
                'mode' => 'sandbox',
                'sort_order' => 1,
                'credentials' => [
                    'merchant_id' => config('payment.gateways.kashier.merchant_id', ''),
                    'api_key' => config('payment.gateways.kashier.api_key', ''),
                    'secret_key' => config('payment.gateways.kashier.secret_key', ''),
                    'currency' => 'EGP',
                ],
            ],
            [
                'code' => 'paymob',
                'name' => 'Paymob',
                'is_active' => true,
                'mode' => 'sandbox',
                'sort_order' => 2,
                'credentials' => [
                    'api_key' => config('payment.gateways.paymob.api_key', ''),
                    'integration_id' => config('payment.gateways.paymob.integration_id', ''),
                    'iframe_id' => config('payment.gateways.paymob.iframe_id', ''),
                    'hmac_secret' => config('payment.gateways.paymob.hmac_secret', ''),
                    'currency' => 'EGP',
                ],
            ],
            [
                'code' => 'stripe',
                'name' => 'Stripe',
                'is_active' => true,
                'mode' => 'sandbox',
                'sort_order' => 3,
                'credentials' => [
                    'public_key' => config('payment.gateways.stripe.public_key', ''),
                    'secret_key' => config('payment.gateways.stripe.secret_key', ''),
                    'webhook_secret' => config('payment.gateways.stripe.webhook_secret', ''),
                    'currency' => 'USD',
                ],
            ],
            [
                'code' => 'paypal',
                'name' => 'PayPal',
                'is_active' => true,
                'mode' => 'sandbox',
                'sort_order' => 4,
                'credentials' => [
                    'client_id' => config('payment.gateways.paypal.client_id', ''),
                    'client_secret' => config('payment.gateways.paypal.client_secret', ''),
                    'currency' => 'USD',
                ],
            ],
            [
                'code' => 'fawry',
                'name' => 'Fawry Pay',
                'is_active' => false,
                'mode' => 'sandbox',
                'sort_order' => 5,
                'credentials' => [
                    'merchant_code' => '',
                    'security_key' => '',
                    'currency' => 'EGP',
                ],
            ],
        ];

        foreach ($defaultGateways as $gwData) {
            PaymentGateway::firstOrCreate(
                ['code' => $gwData['code']],
                [
                    'name' => $gwData['name'],
                    'is_active' => $gwData['is_active'],
                    'mode' => $gwData['mode'],
                    'sort_order' => $gwData['sort_order'],
                    'credentials' => $gwData['credentials'],
                ]
            );
        }
    }
}
