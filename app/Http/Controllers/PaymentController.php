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
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    protected $sectionAccessService;
    protected $paymentGatewayService;

    public function __construct(SectionAccessService $sectionAccessService, PaymentGatewayService $paymentGatewayService)
    {
        $this->middleware('auth');
        $this->sectionAccessService = $sectionAccessService;
        $this->paymentGatewayService = $paymentGatewayService;
    }

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

    public function process(Request $request, Course $course, CourseSection $section = null)
    {
        try {
            $request->validate([
                'gateway' => 'required|in:stripe,paypal,paymob',
            ]);

            // Log the request for debugging
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
            // Create payment record
            $payment = Payment::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'amount' => $course->getEffectivePrice(),
                'currency' => 'USD',
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

            // Process payment through gateway
            $result = $this->paymentGatewayService->processPayment($payment, $request, $request->gateway);

            if ($result['success']) {
                // Payment successful
                $payment->update(['status' => 'completed']);

                // Create enrollment if not already enrolled
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
                }

                return redirect()->route('payment.success', $payment)
                    ->with('success', 'تم الدفع بنجاح! يمكنك الآن الوصول للكورس.');
            } else {
                // Payment failed
                $payment->update(['status' => 'failed']);
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
            // Create payment record
            $payment = Payment::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'amount' => $section->getEffectivePrice(),
                'currency' => 'USD',
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

            // Process payment through gateway
            $result = $this->paymentGatewayService->processPayment($payment, $request, $request->gateway);

            if ($result['success']) {
                // Payment successful
                $payment->update(['status' => 'completed']);

                // Grant section access
                $this->sectionAccessService->grantAccess($user, $section);

                return redirect()->route('payment.success', $payment)
                    ->with('success', 'تم الدفع بنجاح! يمكنك الآن الوصول للقسم.');
            } else {
                // Payment failed
                $payment->update(['status' => 'failed']);
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

    public function success(Payment $payment = null)
    {
        try {
            if ($payment && $payment->user_id !== Auth::id()) {
                abort(403, 'Unauthorized access to payment details.');
            }

            // If no payment provided, get the latest completed payment for the user
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

    public function confirmStripePayment(Request $request)
    {
        try {
            $request->validate([
                'payment_intent_id' => 'required|string'
            ]);

            $result = $this->paymentGatewayService->confirmStripePayment($request->payment_intent_id);

            if ($result['success']) {
                // Find the payment record
                $payment = Payment::where('payment_id', $request->payment_intent_id)->first();

                if ($payment) {
                    $payment->update(['status' => 'completed']);

                    // Create enrollment if it's a course payment and user is not already enrolled
                    if ($payment->course_id) {
                        $existingEnrollment = CourseEnrollment::where('user_id', $payment->user_id)
                            ->where('course_id', $payment->course_id)
                            ->first();

                        if (!$existingEnrollment) {
                            CourseEnrollment::create([
                                'user_id' => $payment->user_id,
                                'course_id' => $payment->course_id,
                                'enrolled_at' => now(),
                                'progress' => 0
                            ]);
                        }
                    }
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

    public function webhook(Request $request, $gateway)
    {
        try {
            Log::info("Webhook received from {$gateway}", [
                'payload' => $request->all(),
                'headers' => $request->headers->all()
            ]);

            // Process webhook based on gateway
            switch ($gateway) {
                case 'stripe':
                    return $this->processStripeWebhook($request);
                case 'paypal':
                    return $this->processPayPalWebhook($request);
                case 'paymob':
                    return $this->processPayMobWebhook($request);
                default:
                    Log::warning("Unknown gateway webhook: {$gateway}");
                    return response()->json(['error' => 'Unknown gateway'], 400);
            }
        } catch (\Exception $e) {
            Log::error("Webhook processing error for {$gateway}: " , [
                'payload' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['error' => 'Webhook processing failed'], 500);
        }
    }

    private function processStripeWebhook(Request $request)
    {
        try {
            // Implement Stripe webhook processing
            // This would verify the webhook signature and process the event

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            Log::error('Error in Stripe webhook: ' , [
                'payload' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['error' => 'Stripe webhook processing failed'], 500);
        }
    }

    private function processPayPalWebhook(Request $request)
    {
        try {
            // Implement PayPal webhook processing

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            Log::error('Error in PayPal webhook: ' , [
                'payload' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['error' => 'PayPal webhook processing failed'], 500);
        }
    }

    private function processPayMobWebhook(Request $request)
    {
        try {
            // Implement PayMob webhook processing

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            Log::error('Error in PayMob webhook: ' , [
                'payload' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['error' => 'PayMob webhook processing failed'], 500);
        }
    }
}
