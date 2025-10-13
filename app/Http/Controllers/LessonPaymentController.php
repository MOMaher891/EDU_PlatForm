<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\LessonPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LessonPaymentController extends Controller
{
    // Admin listing
    public function indexAdmin(Request $request)
    {
        $query = LessonPayment::with(['student', 'course'])->orderByDesc('created_at');

        // Filters
        if ($request->filled('status')) {
            $query->where('status', (int) $request->input('status'));
        }

        if ($request->filled('student')) {
            $term = trim($request->input('student'));
            $query->whereHas('student', function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                  ->orWhere('email', 'like', "%{$term}%");
            });
        }

        if ($request->filled('course')) {
            $term = trim($request->input('course'));
            $query->whereHas('course', function ($q) use ($term) {
                $q->where('title', 'like', "%{$term}%");
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }

        $payments = $query->paginate(20)->appends($request->query());

        return view('admin.lesson_payments.index', [
            'payments' => $payments,
            'filters' => [
                'status' => $request->input('status'),
                'student' => $request->input('student'),
                'course' => $request->input('course'),
                'date_from' => $request->input('date_from'),
                'date_to' => $request->input('date_to'),
            ],
        ]);
    }

    // Admin show
    public function showAdmin(LessonPayment $payment)
    {
        $payment->load(['student', 'course']);
        return view('admin.lesson_payments.show', compact('payment'));
    }

    // Instructor listing (only their courses)
    public function indexInstructor(Request $request)
    {
        $userId = Auth::id();
        $payments = LessonPayment::with(['student', 'course'])
            ->whereHas('course', function ($q) use ($userId) {
                $q->where('instructor_id', $userId);
            })
            ->orderByDesc('created_at')
            ->paginate(20);
        return view('instructor.lesson_payments.index', compact('payments'));
    }

    // Instructor show
    public function showInstructor(LessonPayment $payment)
    {
        if ($payment->course->instructor_id !== Auth::id()) {
            abort(403);
        }
        $payment->load(['student', 'course']);
        return view('instructor.lesson_payments.show', compact('payment'));
    }

    public function create(Course $course)
    {
        $course->load('sections.lessons');
        $paidLessonIds = LessonPayment::where('student_id', Auth::id())
            ->where('course_id', $course->id)
            ->where('status', 1)
            ->get()
            ->flatMap(function ($p) {
                return collect(explode(',', (string) $p->lessons_ids))
                    ->filter()
                    ->map(fn($id) => (int) $id);
            })
            ->unique()
            ->values()
            ->all();

        return view('student.payments.select-lessons', compact('course', 'paidLessonIds'));
    }

    public function store(Request $request, Course $course)
    {
        $data = $request->validate([
            'lessons' => 'required|array|min:1',
            'lessons.*' => 'integer|exists:lessons,id',
            'attachment' => 'required|file|max:10240',
        ]);

        $lessons = Lesson::whereIn('id', $data['lessons'])
            ->whereHas('section', function ($q) use ($course) {
                $q->where('course_id', $course->id);
            })->get();

        $total = $lessons->sum(function ($l) {
            return (float) ($l->price ?? 0);
        });

        $path = $request->file('attachment')->store('lesson-payments', 'public');

        $payment = LessonPayment::create([
            'student_id' => Auth::id(),
            'course_id' => $course->id,
            'lessons_ids' => implode(',', $lessons->pluck('id')->all()),
            'total_cost' => $total,
            'attachment_path' => $path,
            'status' => 0,
        ]);

        return redirect()->route('student.courses.show', $course)
            ->with('success', 'تم إرسال طلب الدفع بنجاح. بانتظار المراجعة.');
    }

    public function updateStatus(Request $request, LessonPayment $payment)
    {
        $request->validate([
            'status' => 'required|in:0,1,2',
        ]);

        $user = Auth::user();
        $isAdmin = method_exists($user, 'isAdmin') ? $user->isAdmin() : false;
        $isInstructorOwner = method_exists($user, 'isInstructor') && $user->isInstructor() && ($payment->course->instructor_id === $user->id);

        if (!$isAdmin && !$isInstructorOwner) {
            abort(403);
        }

        $payment->update(['status' => (int) $request->status]);

        return back()->with('success', 'تم تحديث حالة الدفع.');
    }
}


