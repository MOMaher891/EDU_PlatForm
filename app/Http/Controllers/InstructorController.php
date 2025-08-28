<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Category;
use App\Models\CourseSection;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class InstructorController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'instructor']);
    }

    public function dashboard()
    {
        $instructor = Auth::user();
        
        // Statistics
        $totalCourses = $instructor->instructedCourses()->count();
        $totalStudents = $instructor->instructedCourses()
            ->withCount('enrollments')
            ->get()
            ->sum('enrollments_count');
        $totalRevenue = $instructor->payments()->where('status', 'completed')->sum('amount');
        $averageRating = $instructor->instructedCourses()
            ->withAvg('reviews', 'rating')
            ->get()
            ->avg('reviews_avg_rating') ?? 0;
        
        // Recent courses
        $recentCourses = $instructor->instructedCourses()
            ->withCount('enrollments')
            ->latest()
            ->take(5)
            ->get();
        
        // Recent students
        $recentStudents = User::whereHas('enrollments.course', function($query) use ($instructor) {
            $query->where('instructor_id', $instructor->id);
        })->with(['enrollments' => function($query) use ($instructor) {
            $query->whereHas('course', function($q) use ($instructor) {
                $q->where('instructor_id', $instructor->id);
            });
        }])->latest()->take(10)->get();
        
        // Monthly statistics
        $monthlyStats = [
            'courses' => $instructor->instructedCourses()
                ->whereMonth('created_at', now()->month)->count(),
            'students' => User::whereHas('enrollments.course', function($query) use ($instructor) {
                $query->where('instructor_id', $instructor->id);
            })->whereMonth('created_at', now()->month)->count(),
            'revenue' => $instructor->payments()
                ->where('status', 'completed')
                ->whereMonth('created_at', now()->month)
                ->sum('amount')
        ];

        return view('instructor.dashboard', compact(
            'totalCourses',
            'totalStudents',
            'totalRevenue',
            'averageRating',
            'recentCourses',
            'recentStudents',
            'monthlyStats'
        ));
    }

    public function courses(Request $request)
    {
        $query = Auth::user()->instructedCourses()->with('category');
        
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }
        
        if ($request->filled('status')) {
            $query->where('is_published', $request->status === 'published');
        }
        
        $courses = $query->withCount('enrollments')
            ->orderBy('created_at', 'desc')
            ->paginate(12);
            
        return view('instructor.courses.index', compact('courses'));
    }

    public function createCourse()
    {
        $categories = Category::all();
        
        return view('instructor.courses.create', compact('categories'));
    }

    public function storeCourse(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'short_description' => 'required|string|max:500',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0|lt:price',
            'level' => 'required|in:beginner,intermediate,advanced',
            'duration_hours' => 'required|integer|min:1',
            'thumbnail' => 'nullable|image|max:2048'
        ]);
        
        $data = $request->all();
        $data['instructor_id'] = Auth::id();
        $data['is_published'] = false; // Default to draft
        
        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('courses', 'public');
        }
        
        $course = Course::create($data);
        
        return redirect()->route('instructor.courses.show', $course)
            ->with('success', 'تم إنشاء الكورس بنجاح');
    }

    public function showCourse(Course $course)
    {
        // Ensure instructor can only view their own courses
        if ($course->instructor_id !== Auth::id()) {
            abort(403);
        }
        
        $course->load(['category', 'sections.lessons', 'enrollments.user']);
        
        return view('instructor.courses.show', compact('course'));
    }

    public function editCourse(Course $course)
    {
        // Ensure instructor can only edit their own courses
        if ($course->instructor_id !== Auth::id()) {
            abort(403);
        }
        
        $categories = Category::all();
        
        return view('instructor.courses.edit', compact('course', 'categories'));
    }

    public function updateCourse(Request $request, Course $course)
    {
        // Ensure instructor can only update their own courses
        if ($course->instructor_id !== Auth::id()) {
            abort(403);
        }
        
        $request->validate([
            'title' => 'required|string|max:255',
            'short_description' => 'required|string|max:500',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0|lt:price',
            'level' => 'required|in:beginner,intermediate,advanced',
            'duration_hours' => 'required|integer|min:1',
            'thumbnail' => 'nullable|image|max:2048',
            'is_published' => 'boolean'
        ]);
        
        $data = $request->all();
        
        if ($request->hasFile('thumbnail')) {
            if ($course->thumbnail) {
                Storage::disk('public')->delete($course->thumbnail);
            }
            $data['thumbnail'] = $request->file('thumbnail')->store('courses', 'public');
        }
        
        $course->update($data);
        
        return redirect()->route('instructor.courses.show', $course)
            ->with('success', 'تم تحديث الكورس بنجاح');
    }

    public function deleteCourse(Course $course)
    {
        // Ensure instructor can only delete their own courses
        if ($course->instructor_id !== Auth::id()) {
            abort(403);
        }
        
        if ($course->enrollments()->count() > 0) {
            return back()->with('error', 'لا يمكن حذف كورس يحتوي على طلاب مسجلين');
        }
        
        if ($course->thumbnail) {
            Storage::disk('public')->delete($course->thumbnail);
        }
        
        $course->delete();
        
        return redirect()->route('instructor.courses.index')
            ->with('success', 'تم حذف الكورس بنجاح');
    }

    public function students()
    {
        $instructor = Auth::user();
        
        $students = User::whereHas('enrollments.course', function($query) use ($instructor) {
            $query->where('instructor_id', $instructor->id);
        })->with(['enrollments.course' => function($query) use ($instructor) {
            $query->where('instructor_id', $instructor->id);
        }])->paginate(20);
        
        return view('instructor.students.index', compact('students'));
    }

    public function showStudent(User $student)
    {
        $instructor = Auth::user();
        
        // Get student's enrollments in instructor's courses
        $enrollments = $student->enrollments()
            ->whereHas('course', function($query) use ($instructor) {
                $query->where('instructor_id', $instructor->id);
            })
            ->with('course')
            ->get();
        
        if ($enrollments->isEmpty()) {
            abort(404);
        }
        
        return view('instructor.students.show', compact('student', 'enrollments'));
    }

    public function earnings()
    {
        $instructor = Auth::user();
        
        $totalEarnings = $instructor->payments()
            ->where('status', 'completed')
            ->sum('amount');
        
        $monthlyEarnings = $instructor->payments()
            ->where('status', 'completed')
            ->whereMonth('created_at', now()->month)
            ->sum('amount');
        
        $recentPayments = $instructor->payments()
            ->with(['user', 'course'])
            ->where('status', 'completed')
            ->latest()
            ->take(10)
            ->get();
        
        return view('instructor.earnings.index', compact(
            'totalEarnings',
            'monthlyEarnings',
            'recentPayments'
        ));
    }
}
