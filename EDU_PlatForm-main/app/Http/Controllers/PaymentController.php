<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseSection;
use App\Models\Payment;
use App\Models\CourseEnrollment;
use App\Services\SectionAccessService;
use App\Services\PaymentGatewayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    protected $sectionAccessService;
    protected $paymentGatewayService;

    public function __construct(SectionAccessService $sectionAccessService, PaymentGatewayService $paymentGatewayService)
    {
        $this->middleware('auth')->except(['webhook']);
        $this->sectionAccessService = $sectionAccessService;
        $this->paymentGatewayService = $paymentGatewayService;
    }

    /**
     * Show checkout page for course or section.
     */
    public function checkout(Course $course, CourseSection $section = null)
    {
        try {
            $user = Auth::user();

            if ($section) {
                // Section purchase
                return $this->checkoutSection($course, $section, $user);
            } else {
                // Full course purchase
                return $this->checkoutCourse($course, $user);
            }
        } catch (\Exception $e) {
            Log::error('Error in checkout: ' , [
                'user_id' => Auth::id(),
                'course_id' => $course->id ?? null,
                'section_id' => $section->id ?? null,
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'حدث خطأ أثناء تحميل صفحة الدفع. يرجى المحاولة مرة أخرى.');
        }
    }

    private function checkoutCourse(Course $course, $user)
    {
        try {
            // Check if already enrolled
            if ($user->enrollments()->where('course_id', $course->id)->exists()) {
                return redirect()->route('student.courses.show', $course)
                    ->with('info', 'أنت مسجل في هذا الكورس بالفعل');
            }

            // If course is free, enroll directly
            if ($course->getEffectivePrice() == 0) {
                return $this->enrollFree($course);
            }

            return view('payment.checkout', compact('course'));
        } catch (\Exception $e) {
            Log::error('Error in checkout course: ' , [
                'user_id' => $user->id ?? null,
                'course_id' => $course->id ?? null,
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'حدث خطأ أثناء تحميل صفحة دفع الكورس. يرجى المحاولة مرة أخرى.');
        }
    }

    private function checkoutSection(Course $course, CourseSection $section, $user)
    {
        try {
            // Check if section is purchasable
            if (!$section->isPurchasable()) {
                return redirect()->route('student.courses.show', $course)
                    ->with('error', 'This section is not available for individual purchase.');
            }

            // Check if already has access
            if ($this->sectionAccessService->hasAccess($user, $section)) {
                return redirect()->route('student.courses.show', $course)
                    ->with('info', 'You already have access to this section.');
            }

            // If section is free, grant access directly
            if ($section->getEffectivePrice() == 0) {
                return $this->grantFreeSectionAccess($course, $section);
            }

            return view('payment.checkout', compact('course', 'section'));
        } catch (\Exception $e) {
            Log::error('Error in checkout section: ' , [
                'user_id' => $user->id ?? null,
                'course_id' => $course->id ?? null,
                'section_id' => $section->id ?? null,
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'حدث خطأ أثناء تحميل صفحة دفع القسم. يرجى المحاولة مرة أخرى.');
        }
    }

    /**
     * Process checkout request.
     */
    public function process(Request $request, Course $course, CourseSection $section = null)
    {
        try {
            $request->validate([
                'gateway' => 'required|in:stripe,paypal,paymob',
            ]);

            Log::info('Payment request received', [
                'request_data' => $request->all(),
                'course_id' => $course->id,
                'user_id' => auth()->id()
            ]);

            $user = Auth::user();

            if ($section) {
                return $this->processSectionPayment($request, $course, $section, $user);
            } else {
                return $this->processCoursePayment($request, $course, $user);
            }
        } catch (\Exception $e) {
            Log::error('Error in process payment: ' , [
                'user_id' => Auth::id(),
                'course_id' => $course->id ?? null,
                'section_id' => $section->id ?? null,
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'حدث خطأ أثناء معالجة الدفع. يرجى المحاولة مرة أخرى.');
        }
    }

    private function processCoursePayment(Request $request, Course $course, $user)
    {
        try {
            // Create pending payment record
            $payment = Payment::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'amount' => $course->getEffectivePrice(),
                'currency' => $request->gateway === 'paymob' ? 'EGP' : 'USD',
                'payment_method' => $request->gateway,
                'payment_id' => 'PAY-' . Str::random(10),
                'status' => 'pending',
                'transaction_data' => [
                    'course_title' => $course->title,
                    'original_price' => $course->price,
                    'discount_price' => $course->discount_price,
                    'effective_price' => $course->getEffectivePrice()
                ]
            ]);

            // Process payment through resolved gateway strategy
            $result = $this->paymentGatewayService->processPayment($payment, $request, $request->gateway);

            if ($result['success']) {
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json($result);
                }

                // If gateway requires client redirect (like Paymob/PayPal)
                if (isset($result['data']['redirect_url'])) {
                    return redirect($result['data']['redirect_url']);
                }

                // If processed synchronously
                $this->completePaymentAndEnroll($payment, $result['data'] ?? []);

                return redirect()->route('payment.success', $payment)
                    ->with('success', 'تم الدفع بنجاح! يمكنك الآن الوصول للكورس.');
            } else {
                $payment->update(['status' => 'failed']);

                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json($result, 400);
                }

                return redirect()->route('payment.failed', $payment)
                    ->with('error', 'فشل في الدفع: ' . $result['message']);
            }
        } catch (\Exception $e) {
            Log::error('Error in process course payment: ' , [
                'user_id' => $user->id ?? null,
                'course_id' => $course->id ?? null,
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'حدث خطأ أثناء معالجة دفع الكورس. يرجى المحاولة مرة أخرى.');
        }
    }

    private function processSectionPayment(Request $request, Course $course, CourseSection $section, $user)
    {
        try {
            // Create pending payment record
            $payment = Payment::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'amount' => $section->getEffectivePrice(),
                'currency' => $request->gateway === 'paymob' ? 'EGP' : 'USD',
                'payment_method' => $request->gateway,
                'payment_id' => 'PAY-' . Str::random(10),
                'status' => 'pending',
                'transaction_data' => [
                    'course_title' => $course->title,
                    'section_title' => $section->title,
                    'section_id' => $section->id,
                    'original_price' => $section->price,
                    'discount_price' => $section->discount_price,
                    'effective_price' => $section->getEffectivePrice()
                ]
            ]);

            // Process payment through resolved gateway strategy
            $result = $this->paymentGatewayService->processPayment($payment, $request, $request->gateway);

            if ($result['success']) {
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json($result);
                }

                // If gateway requires client redirect (like Paymob/PayPal)
                if (isset($result['data']['redirect_url'])) {
                    return redirect($result['data']['redirect_url']);
                }

                // If processed synchronously
                $this->completePaymentAndEnroll($payment, $result['data'] ?? []);

                return redirect()->route('payment.success', $payment)
                    ->with('success', 'تم الدفع بنجاح! يمكنك الآن الوصول للقسم.');
            } else {
                $payment->update(['status' => 'failed']);

                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json($result, 400);
                }

                return redirect()->route('payment.failed', $payment)
                    ->with('error', 'فشل في الدفع: ' . $result['message']);
            }
        } catch (\Exception $e) {
            Log::error('Error in process section payment: ' , [
                'user_id' => $user->id ?? null,
                'course_id' => $course->id ?? null,
                'section_id' => $section->id ?? null,
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'حدث خطأ أثناء معالجة دفع القسم. يرجى المحاولة مرة أخرى.');
        }
    }

    /**
     * Handle unified redirect callbacks from payment gateways.
     */
    public function callback(Request $request, string $gateway)
    {
        try {
            Log::info("Callback received from {$gateway}");

            $result = $this->paymentGatewayService->handleCallback($request, $gateway);

            if ($result['success']) {
                $payment = Payment::where('payment_id', $result['payment_id'])
                    ->orWhere('id', $result['payment_id'])
                    ->first();

                if ($payment) {
                    $this->completePaymentAndEnroll($payment, $result['transaction_data'] ?? []);
                    return redirect()->route('payment.success', $payment)
                        ->with('success', 'تم الدفع بنجاح! تم تفعيل اشتراكك.');
                }
            }

            $paymentId = $result['payment_id'] ?? null;
            $payment = $paymentId ? Payment::where('payment_id', $paymentId)->orWhere('id', $paymentId)->first() : null;

            if ($payment) {
                $payment->update(['status' => 'failed']);
                return redirect()->route('payment.failed', $payment)
                    ->with('error', 'فشلت عملية الدفع أو تم إلغاؤها: ' . ($result['message'] ?? ''));
            }

            return redirect()->route('student.courses.index')
                ->with('error', 'فشلت عملية الدفع أو تم إلغاؤها: ' . ($result['message'] ?? ''));
        } catch (\Exception $e) {
            Log::error("Error handling callback for {$gateway}: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('student.courses.index')->with('error', 'حدث خطأ غير متوقع أثناء معالجة الدفع.');
        }
    }

    /**
     * Unified Webhook router for payment gateways.
     */
    public function webhook(Request $request, $gateway)
    {
        try {
            Log::info("Webhook received from {$gateway}");

            $result = $this->paymentGatewayService->handleWebhook($request, $gateway);

            if ($result['success']) {
                $payment = Payment::where('payment_id', $result['payment_id'])
                    ->orWhere('id', $result['payment_id'])
                    ->first();

                if ($payment) {
                    $this->completePaymentAndEnroll($payment, $result['transaction_data'] ?? []);
                    return response()->json(['status' => 'success']);
                }

                Log::warning("Payment record not found for webhook transaction ID: {$result['payment_id']}");
                return response()->json(['error' => 'Payment record not found'], 404);
            }

            return response()->json(['error' => $result['message'] ?? 'Webhook verification failed'], 400);
        } catch (\Exception $e) {
            Log::error("Webhook processing error for {$gateway}: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['error' => 'Webhook processing failed'], 500);
        }
    }

    /**
     * Safely complete payment status and enroll student (DB Transaction wrapped).
     */
    private function completePaymentAndEnroll(Payment $payment, array $transactionData)
    {
        if ($payment->status === 'completed') {
            return;
        }

        DB::transaction(function () use ($payment, $transactionData) {
            $payment->update([
                'status' => 'completed',
                'transaction_data' => array_merge($payment->transaction_data ?? [], $transactionData)
            ]);

            $user = $payment->user;
            $course = $payment->course;
            $sectionId = $payment->transaction_data['section_id'] ?? null;

            if ($sectionId) {
                $section = CourseSection::find($sectionId);
                if ($section) {
                    $this->sectionAccessService->grantAccess($user, $section, $payment->id, $payment->amount);
                    Log::info('Granted section access to student via payment complete', [
                        'user_id' => $user->id,
                        'section_id' => $sectionId,
                        'payment_id' => $payment->id
                    ]);
                }
            } else {
                $existingEnrollment = CourseEnrollment::where('user_id', $user->id)
                    ->where('course_id', $course->id)
                    ->first();

                if (!$existingEnrollment) {
                    CourseEnrollment::create([
                        'user_id' => $user->id,
                        'course_id' => $course->id,
                        'enrolled_at' => now(),
                        'progress' => 0
                    ]);
                    Log::info('Enrolled student in course via payment complete', [
                        'user_id' => $user->id,
                        'course_id' => $course->id,
                        'payment_id' => $payment->id
                    ]);
                }
            }
        });
    }

    /**
     * Show success page.
     */
    public function success(Payment $payment = null)
    {
        try {
            if ($payment && $payment->user_id !== Auth::id()) {
                abort(403, 'Unauthorized access to payment details.');
            }

            if (!$payment) {
                $payment = Payment::where('user_id', Auth::id())
                    ->where('status', 'completed')
                    ->latest()
                    ->first();
            }

            return view('payment.success', compact('payment'));
        } catch (\Exception $e) {
            Log::error('Error in payment success: ' , [
                'user_id' => Auth::id(),
                'payment_id' => $payment->id ?? null,
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'حدث خطأ أثناء عرض صفحة نجاح الدفع. يرجى المحاولة مرة أخرى.');
        }
    }

    /**
     * Show failed page.
     */
    public function failed(Payment $payment)
    {
        try {
            if ($payment->user_id !== Auth::id()) {
                abort(403, 'Unauthorized access to payment details.');
            }

            return view('payment.failed', compact('payment'));
        } catch (\Exception $e) {
            Log::error('Error in payment failed: ' , [
                'user_id' => Auth::id(),
                'payment_id' => $payment->id ?? null,
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'حدث خطأ أثناء عرض صفحة فشل الدفع. يرجى المحاولة مرة أخرى.');
        }
    }

    /**
     * Handle Stripe redirect / confirmation endpoint.
     */
    public function confirmStripePayment(Request $request)
    {
        try {
            $request->validate([
                'payment_intent_id' => 'required|string'
            ]);

            $result = $this->paymentGatewayService->confirmStripePayment($request->payment_intent_id);

            if ($result['success']) {
                $payment = Payment::where('payment_id', $request->payment_intent_id)->first();

                if ($payment) {
                    $this->completePaymentAndEnroll($payment, ['verified_via' => 'confirm_endpoint']);
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Payment confirmed successfully'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['message']
                ], 400);
            }
        } catch (\Exception $e) {
            Log::error('Error confirming Stripe payment: ' , [
                'payment_intent_id' => $request->payment_intent_id ?? null,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error confirming payment'
            ], 500);
        }
    }

    /**
     * Handle cancel event redirect.
     */
    public function cancel()
    {
        return redirect()->route('student.courses.index')
            ->with('warning', 'تم إلغاء عملية الدفع.');
    }

    private function enrollFree(Course $course)
    {
        $user = Auth::user();
        CourseEnrollment::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'enrolled_at' => now(),
            'progress' => 0
        ]);

        return redirect()->route('student.courses.show', $course)
            ->with('success', 'تم التسجيل في الكورس المجاني بنجاح!');
    }

    private function grantFreeSectionAccess(Course $course, CourseSection $section)
    {
        $user = Auth::user();
        $this->sectionAccessService->grantAccess($user, $section, null, 0);

        return redirect()->route('student.courses.show', $course)
            ->with('success', 'تم تفعيل الوصول للقسم المجاني بنجاح!');
    }
}
