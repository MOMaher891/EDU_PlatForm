<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Course;
use App\Models\Category;
use App\Models\CourseEnrollment;
use App\Models\Payment;
use App\Models\CourseSection;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function dashboard()
    {
        try {
            // Statistics
            $totalUsers = User::count();
            $totalCourses = Course::count();
            $totalEnrollments = CourseEnrollment::count();
            $totalRevenue = Payment::where('status', 'completed')->sum('amount');

            // Recent activities
            $recentUsers = User::latest()->take(5)->get();
            $recentCourses = Course::with('instructor')->latest()->take(5)->get();
            $recentEnrollments = CourseEnrollment::with(['user', 'course'])
                ->latest()->take(10)->get();

            // Monthly statistics
            $monthlyStats = [
                'users' => User::whereMonth('created_at', now()->month)->count(),
                'courses' => Course::whereMonth('created_at', now()->month)->count(),
                'enrollments' => CourseEnrollment::whereMonth('created_at', now()->month)->count(),
                'revenue' => Payment::where('status', 'completed')
                    ->whereMonth('created_at', now()->month)->sum('amount')
            ];

            // Top courses
            $topCourses = Course::withCount('enrollments')
                ->orderBy('enrollments_count', 'desc')
                ->take(10)
                ->get();

            return view('admin.dashboard', compact(
                'totalUsers',
                'totalCourses',
                'totalEnrollments',
                'totalRevenue',
                'recentUsers',
                'recentCourses',
                'recentEnrollments',
                'monthlyStats',
                'topCourses'
            ));
        } catch (\Exception $e) {
            Log::error('Error in admin dashboard: ' , [
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'حدث خطأ أثناء تحميل لوحة التحكم. يرجى المحاولة مرة أخرى.');
        }
    }

    // Users Management
    public function users(Request $request)
    {
        try {
            $query = User::query();

            if ($request->filled('search')) {
                $query->where(function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%')
                        ->orWhere('email', 'like', '%' . $request->search . '%');
                });
            }

            if ($request->filled('role')) {
                $query->where('role', $request->role);
            }
            if ($request->filled('sort')) {
                if ($request->filled('sort_type')) {
                    $query->orderBy($request->sort, $request->sort_type);
                } else {
                    $query->orderBy($request->sort, 'desc');
                }
            }

            if ($request->filled('is_active')) {
                if ($request->is_active == 1) {
                    $query->where('deleted_at', NULL);
                } else {
                    $query->where('deleted_at', '!=', NULL)->orWhere('is_active', 0);
                }
            }

            $users = $query->withCount(['enrollments', 'instructedCourses'])
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            return view('admin.users.index', compact('users'));
        } catch (\Exception $e) {
            Log::error('Error in admin users: ' , [
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'حدث خطأ أثناء تحميل قائمة المستخدمين. يرجى المحاولة مرة أخرى.');
        }
    }

    public function showUser(User $user)
    {
        try {
            $user->load(['enrollments.course', 'instructedCourses', 'activities']);
            return view('admin.users.show', compact('user'));
        } catch (\Exception $e) {
            Log::error('Error in show user: ' , [
                'user_id' => auth()->id(),
                'target_user_id' => $user->id ?? null,
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'حدث خطأ أثناء عرض بيانات المستخدم. يرجى المحاولة مرة أخرى.');
        }
    }

    public function updateUser(Request $request, User $user)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
                'role' => 'required|in:student,instructor,admin',
                'is_active' => 'boolean'
            ]);

            $user->update($validated);

            return redirect()->route('admin.users.index')
                ->with('success', 'تم تحديث المستخدم بنجاح');
        } catch (\Exception $e) {
            Log::error('Error in update user: ' , [
                'user_id' => auth()->id(),
                'target_user_id' => $user->id ?? null,
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'حدث خطأ أثناء تحديث المستخدم. يرجى المحاولة مرة أخرى.');
        }
    }

    public function destroyUser(User $user)
    {
        try {
            if ($user->id === auth()->id()) {
                return redirect()->back()->with('error', 'لا يمكنك حذف حسابك الخاص');
            }

            $user->delete();

            return redirect()->route('admin.users.index')
                ->with('success', 'تم حذف المستخدم بنجاح');
        } catch (\Exception $e) {
            Log::error('Error in destroy user: ' , [
                'user_id' => auth()->id(),
                'target_user_id' => $user->id ?? null,
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'حدث خطأ أثناء حذف المستخدم. يرجى المحاولة مرة أخرى.');
        }
    }

    public function createUser()
    {
        try {
            return view('admin.users.create');
        } catch (\Exception $e) {
            Log::error('Error in create user form: ' , [
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'حدث خطأ أثناء تحميل نموذج إنشاء المستخدم. يرجى المحاولة مرة أخرى.');
        }
    }

    public function storeUser(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
                'role' => 'required|in:student,instructor,admin',
                'is_active' => 'boolean'
            ]);

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => $validated['role'],
                'is_active' => $validated['is_active'] ?? true,
                'email_verified_at' => now()
            ]);

            return redirect()->route('admin.users.index')
                ->with('success', 'تم إنشاء المستخدم بنجاح');
        } catch (\Exception $e) {
            Log::error('Error in store user: ' , [
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'حدث خطأ أثناء إنشاء المستخدم. يرجى المحاولة مرة أخرى.');
        }
    }

    public function editUser(User $user)
    {
        try {
            return view('admin.users.edit', compact('user'));
        } catch (\Exception $e) {
            Log::error('Error in edit user form: ' , [
                'user_id' => auth()->id(),
                'target_user_id' => $user->id ?? null,
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'حدث خطأ أثناء تحميل نموذج تعديل المستخدم. يرجى المحاولة مرة أخرى.');
        }
    }

    // Courses Management
    public function courses(Request $request)
    {
        try {
            $query = Course::with(['instructor', 'category']);

            if ($request->filled('search')) {
                $query->where(function ($q) use ($request) {
                    $q->where('title', 'like', '%' . $request->search . '%')
                        ->orWhere('description', 'like', '%' . $request->search . '%');
                });
            }

            if ($request->filled('category')) {
                $query->where('category_id', $request->category);
            }

            if ($request->filled('status')) {
                $query->where('is_published', $request->status === 'published');
            }

            $courses = $query->orderBy('created_at', 'desc')->paginate(20);
            $categories = Category::all();

            return view('admin.courses.index', compact('courses', 'categories'));
        } catch (\Exception $e) {
            Log::error('Error in admin courses: ' , [
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'حدث خطأ أثناء تحميل قائمة الكورسات. يرجى المحاولة مرة أخرى.');
        }
    }

    public function showCourse(Course $course)
    {
        try {
            $course->load(['sections.lessons', 'instructor', 'category', 'enrollments.user']);
            return view('admin.courses.show', compact('course'));
        } catch (\Exception $e) {
            Log::error('Error in show course: ' , [
                'user_id' => auth()->id(),
                'course_id' => $course->id ?? null,
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'حدث خطأ أثناء عرض تفاصيل الكورس. يرجى المحاولة مرة أخرى.');
        }
    }

    public function createCourse()
    {
        try {
            $categories = Category::all();
            $instructors = User::where('role', 'instructor')->get();
            return view('admin.courses.create', compact('categories', 'instructors'));
        } catch (\Exception $e) {
            Log::error('Error in create course form: ' , [
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'حدث خطأ أثناء تحميل نموذج إنشاء الكورس. يرجى المحاولة مرة أخرى.');
        }
    }

    public function storeCourse(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'short_description' => 'required|string|max:500',
                'category_id' => 'required|exists:categories,id',
                'instructor_id' => 'required|exists:users,id',
                'price' => 'required|numeric|min:0',
                'level' => 'required|in:beginner,intermediate,advanced',
                'duration_hours' => 'required|integer|min:1',
                'is_published' => 'boolean',
                'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            if ($request->hasFile('thumbnail')) {
                $thumbnailPath = $request->file('thumbnail')->store('courses/thumbnails', 'public');
                $validated['thumbnail'] = $thumbnailPath;
            }

            $course = Course::create($validated);

            return redirect()->route('admin.courses.index')
                ->with('success', 'تم إنشاء الكورس بنجاح');
        } catch (\Exception $e) {
            Log::error('Error in store course: ' , [
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'حدث خطأ أثناء إنشاء الكورس. يرجى المحاولة مرة أخرى.');
        }
    }

    public function editCourse(Course $course)
    {
        try {
            $categories = Category::all();
            $instructors = User::where('role', 'instructor')->get();
            return view('admin.courses.edit', compact('course', 'categories', 'instructors'));
        } catch (\Exception $e) {
            Log::error('Error in edit course form: ' , [
                'user_id' => auth()->id(),
                'course_id' => $course->id ?? null,
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'حدث خطأ أثناء تحميل نموذج تعديل الكورس. يرجى المحاولة مرة أخرى.');
        }
    }

    public function updateCourse(Request $request, Course $course)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'short_description' => 'required|string|max:500',
                'category_id' => 'required|exists:categories,id',
                'instructor_id' => 'required|exists:users,id',
                'price' => 'required|numeric|min:0',
                'level' => 'required|in:beginner,intermediate,advanced',
                'duration_hours' => 'required|integer|min:1',
                'is_published' => 'boolean',
                'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            if ($request->hasFile('thumbnail')) {
                // Delete old thumbnail
                if ($course->thumbnail) {
                    Storage::disk('public')->delete($course->thumbnail);
                }
                $thumbnailPath = $request->file('thumbnail')->store('courses/thumbnails', 'public');
                $validated['thumbnail'] = $thumbnailPath;
            }

            $course->update($validated);

            return redirect()->route('admin.courses.index')
                ->with('success', 'تم تحديث الكورس بنجاح');
        } catch (\Exception $e) {
            Log::error('Error in update course: ' , [
                'user_id' => auth()->id(),
                'course_id' => $course->id ?? null,
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'حدث خطأ أثناء تحديث الكورس. يرجى المحاولة مرة أخرى.');
        }
    }

    public function destroyCourse(Course $course)
    {
        try {
            // Delete thumbnail
            if ($course->thumbnail) {
                Storage::disk('public')->delete($course->thumbnail);
            }

            $course->delete();

            return redirect()->route('admin.courses.index')
                ->with('success', 'تم حذف الكورس بنجاح');
        } catch (\Exception $e) {
            Log::error('Error in destroy course: ' , [
                'user_id' => auth()->id(),
                'course_id' => $course->id ?? null,
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'حدث خطأ أثناء حذف الكورس. يرجى المحاولة مرة أخرى.');
        }
    }

    // Sections Management
    public function sections(Course $course)
    {
        try {
            $sections = $course->sections()->withCount('lessons')->orderBy('order_index')->get();
            return view('admin.sections.index', compact('course', 'sections'));
        } catch (\Exception $e) {
            Log::error('Error in admin sections: ' , [
                'user_id' => auth()->id(),
                'course_id' => $course->id ?? null,
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'حدث خطأ أثناء تحميل قسم الكورس. يرجى المحاولة مرة أخرى.');
        }
    }

    public function createSection(Course $course)
    {
        try {
            return view('admin.sections.create', compact('course'));
        } catch (\Exception $e) {
            Log::error('Error in create section form: ' , [
                'user_id' => auth()->id(),
                'course_id' => $course->id ?? null,
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'حدث خطأ أثناء تحميل نموذج إنشاء القسم. يرجى المحاولة مرة أخرى.');
        }
    }

    public function storeSection(Request $request, Course $course)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'order_index' => 'nullable|integer|min:0',
                'is_active' => 'boolean',
                'price' => 'nullable|numeric|min:0',
                'discount_price' => 'nullable|numeric|min:0|lt:price',
                'is_purchasable_separately' => 'boolean'
            ]);

            $data = $request->all();
            $data['course_id'] = $course->id;
            $data['is_active'] = $request->has('is_active');
            $data['is_purchasable_separately'] = $request->has('is_purchasable_separately');

            // If no order_index provided, set it to the next available index
            if (!isset($data['order_index'])) {
                $data['order_index'] = $course->sections()->max('order_index') + 1;
            }

            CourseSection::create($data);

            return redirect()->route('admin.sections.index', $course)
                ->with('success', 'تم إنشاء القسم بنجاح');
        } catch (\Exception $e) {
            Log::error('Error in store section: ' , [
                'user_id' => auth()->id(),
                'course_id' => $course->id ?? null,
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'حدث خطأ أثناء إنشاء القسم. يرجى المحاولة مرة أخرى.');
        }
    }

    public function editSection(CourseSection $section)
    {
        try {
            return view('admin.sections.edit', compact('section'));
        } catch (\Exception $e) {
            Log::error('Error in edit section form: ' , [
                'user_id' => auth()->id(),
                'section_id' => $section->id ?? null,
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'حدث خطأ أثناء تحميل نموذج تعديل القسم. يرجى المحاولة مرة أخرى.');
        }
    }

    public function updateSection(Request $request, CourseSection $section)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'order_index' => 'nullable|integer|min:0',
                'is_active' => 'boolean',
                'price' => 'nullable|numeric|min:0',
                'discount_price' => 'nullable|numeric|min:0|lt:price',
                'is_purchasable_separately' => 'boolean'
            ]);

            $data = $request->all();
            $data['is_active'] = $request->has('is_active');
            $data['is_purchasable_separately'] = $request->has('is_purchasable_separately');
            $section->update($data);

            return redirect()->route('admin.sections.index', $section->course)
                ->with('success', 'تم تحديث القسم بنجاح');
        } catch (\Exception $e) {
            Log::error('Error in update section: ' , [
                'user_id' => auth()->id(),
                'section_id' => $section->id ?? null,
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'حدث خطأ أثناء تحديث القسم. يرجى المحاولة مرة أخرى.');
        }
    }

    public function destroySection(CourseSection $section)
    {
        try {
            if ($section->lessons()->count() > 0) {
                return redirect()->back()->with('error', 'لا يمكن حذف القسم لوجود دروس مرتبطة به');
            }

            $course = $section->course;
            $section->delete();

            return redirect()->route('admin.sections.index', $course)
                ->with('success', 'تم حذف القسم بنجاح');
        } catch (\Exception $e) {
            Log::error('Error in destroy section: ' , [
                'user_id' => auth()->id(),
                'section_id' => $section->id ?? null,
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'حدث خطأ أثناء حذف القسم. يرجى المحاولة مرة أخرى.');
        }
    }

    // Lessons Management
    public function lessons(CourseSection $section)
    {
        try {
            $lessons = $section->lessons()->orderBy('order_index')->get();
            return view('admin.lessons.index', compact('section', 'lessons'));
        } catch (\Exception $e) {
            Log::error('Error in admin lessons: ' , [
                'user_id' => auth()->id(),
                'section_id' => $section->id ?? null,
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'حدث خطأ أثناء تحميل دروس القسم. يرجى المحاولة مرة أخرى.');
        }
    }

    public function createLesson(CourseSection $section)
    {
        try {
            return view('admin.lessons.create', compact('section'));
        } catch (\Exception $e) {
            Log::error('Error in create lesson form: ' , [
                'user_id' => auth()->id(),
                'section_id' => $section->id ?? null,
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'حدث خطأ أثناء تحميل نموذج إنشاء الدرس. يرجى المحاولة مرة أخرى.');
        }
    }

    public function storeLesson(Request $request, CourseSection $section)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'nullable|string',
                'video_url' => 'nullable|url|max:500',
                'video_duration' => 'nullable|integer|min:0',
                'file_path' => 'nullable|string|max:500',
                'file_type' => 'required|in:video,pdf,document,quiz,image',
                'order_index' => 'nullable|integer|min:0',
                'is_free' => 'boolean',
                'is_active' => 'boolean',
                'lesson_file' => 'nullable|file|max:102400' // 100MB max - removed mimes validation for now
            ]);

            $data = $request->all();
            $data['section_id'] = $section->id;
            $data['is_free'] = $request->has('is_free');
            $data['is_active'] = $request->has('is_active');

            // Handle file upload
            if ($request->hasFile('lesson_file') && $request->file('lesson_file')->isValid()) {
                $file = $request->file('lesson_file');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('lessons', $fileName, 'public');

                $data['file_path'] = $filePath;
                $data['file_name'] = $file->getClientOriginalName();
                $data['file_size'] = $file->getSize();
                $data['mime_type'] = $file->getMimeType();

                // Update file_type based on mime type if not explicitly set
                if (str_starts_with($data['mime_type'], 'video/')) {
                    $data['file_type'] = 'video';
                } elseif (str_starts_with($data['mime_type'], 'image/')) {
                    $data['file_type'] = 'image';
                } elseif ($data['mime_type'] === 'application/pdf') {
                    $data['file_type'] = 'pdf';
                } elseif (
                    in_array($data['mime_type'], [
                        'application/msword',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        'application/vnd.ms-excel',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'application/vnd.ms-powerpoint',
                        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                        'text/plain'
                    ])
                ) {
                    $data['file_type'] = 'document';
                }
            }

            // If no order_index provided, set it to the next available index
            if (!isset($data['order_index'])) {
                $data['order_index'] = $section->lessons()->max('order_index') + 1;
            }

            Lesson::create($data);

            return redirect()->route('admin.lessons.index', $section)
                ->with('success', 'تم إنشاء الدرس بنجاح');
        } catch (\Exception $e) {
            Log::error('Error in store lesson: ' , [
                'user_id' => auth()->id(),
                'section_id' => $section->id ?? null,
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'حدث خطأ أثناء إنشاء الدرس. يرجى المحاولة مرة أخرى.');
        }
    }

    public function editLesson(Lesson $lesson)
    {
        try {
            return view('admin.lessons.edit', compact('lesson'));
        } catch (\Exception $e) {
            Log::error('Error in edit lesson form: ' , [
                'user_id' => auth()->id(),
                'lesson_id' => $lesson->id ?? null,
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'حدث خطأ أثناء تحميل نموذج تعديل الدرس. يرجى المحاولة مرة أخرى.');
        }
    }

    public function updateLesson(Request $request, Lesson $lesson)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'nullable|string',
                'video_url' => 'nullable|url|max:500',
                'video_duration' => 'nullable|integer|min:0',
                'file_path' => 'nullable|string|max:500',
                'file_type' => 'required|in:video,pdf,document,quiz,image',
                'order_index' => 'nullable|integer|min:0',
                'is_free' => 'boolean',
                'is_active' => 'boolean',
                'lesson_file' => 'nullable|file|max:102400' // 100MB max - removed mimes validation for now
            ]);

            $data = $request->all();
            $data['is_free'] = $request->has('is_free');
            $data['is_active'] = $request->has('is_active');

            // Handle file upload
            if ($request->hasFile('lesson_file') && $request->file('lesson_file')->isValid()) {
                // Delete old file if exists
                if ($lesson->file_path && Storage::disk('public')->exists($lesson->file_path)) {
                    Storage::disk('public')->delete($lesson->file_path);
                }

                $file = $request->file('lesson_file');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('lessons', $fileName, 'public');

                $data['file_path'] = $filePath;
                $data['file_name'] = $file->getClientOriginalName();
                $data['file_size'] = $file->getSize();
                $data['mime_type'] = $file->getMimeType();

                // Update file_type based on mime type if not explicitly set
                if (str_starts_with($data['mime_type'], 'video/')) {
                    $data['file_type'] = 'video';
                } elseif (str_starts_with($data['mime_type'], 'image/')) {
                    $data['file_type'] = 'image';
                } elseif ($data['mime_type'] === 'application/pdf') {
                    $data['file_type'] = 'pdf';
                } elseif (
                    in_array($data['mime_type'], [
                        'application/msword',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        'application/vnd.ms-excel',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'application/vnd.ms-powerpoint',
                        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                        'text/plain'
                    ])
                ) {
                    $data['file_type'] = 'document';
                }
            }

            $lesson->update($data);

            return redirect()->route('admin.lessons.index', $lesson->section)
                ->with('success', 'تم تحديث الدرس بنجاح');
        } catch (\Exception $e) {
            Log::error('Error in update lesson: ' , [
                'user_id' => auth()->id(),
                'lesson_id' => $lesson->id ?? null,
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'حدث خطأ أثناء تحديث الدرس. يرجى المحاولة مرة أخرى.');
        }
    }

    public function destroyLesson(Lesson $lesson)
    {
        try {
            $section = $lesson->section;

            // Delete the associated file if it exists
            if ($lesson->file_path && Storage::disk('public')->exists($lesson->file_path)) {
                Storage::disk('public')->delete($lesson->file_path);
            }

            $lesson->delete();

            return redirect()->route('admin.lessons.index', $section)
                ->with('success', 'تم حذف الدرس بنجاح');
        } catch (\Exception $e) {
            Log::error('Error in destroy lesson: ' , [
                'user_id' => auth()->id(),
                'lesson_id' => $lesson->id ?? null,
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'حدث خطأ أثناء حذف الدرس. يرجى المحاولة مرة أخرى.');
        }
    }

    public function showLesson(Lesson $lesson)
    {
        try {
            $lesson->load(['section.course']);
            return view('admin.lessons.show', compact('lesson'));
        } catch (\Exception $e) {
            Log::error('Error in show lesson: ' , [
                'user_id' => auth()->id(),
                'lesson_id' => $lesson->id ?? null,
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'حدث خطأ أثناء عرض تفاصيل الدرس. يرجى المحاولة مرة أخرى.');
        }
    }

    /**
     * Download lesson file
     */
    public function downloadLessonFile(Lesson $lesson)
    {
        try {
            // Check if user is enrolled in the course or is admin/instructor
            $user = auth()->user();
            $course = $lesson->section->course;

            if (!$user->isAdmin() && !$user->isInstructor() && !$course->enrollments()->where('user_id', $user->id)->exists()) {
                abort(403, 'You must be enrolled in this course to download lesson files.');
            }

            if (!$lesson->hasFile()) {
                abort(404, 'File not found.');
            }

            $filePath = storage_path('app/public/' . $lesson->file_path);

            if (!file_exists($filePath)) {
                abort(404, 'File not found.');
            }

            return response()->download($filePath, $lesson->file_name ?? basename($lesson->file_path));
        } catch (\Exception $e) {
            Log::error('Error in download lesson file: ' , [
                'user_id' => auth()->id(),
                'lesson_id' => $lesson->id ?? null,
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'حدث خطأ أثناء تحميل ملف الدرس. يرجى المحاولة مرة أخرى.');
        }
    }

    // Categories Management
    public function categories()
    {
        try {
            $categories = Category::withCount('courses')->orderBy('created_at', 'desc')->paginate(20);
            return view('admin.categories.index', compact('categories'));
        } catch (\Exception $e) {
            Log::error('Error in admin categories: ' , [
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'حدث خطأ أثناء تحميل قائمة التصنيفات. يرجى المحاولة مرة أخرى.');
        }
    }

    public function storeCategory(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:categories',
                'description' => 'nullable|string'
            ]);

            Category::create($validated);

            return redirect()->route('admin.categories.index')
                ->with('success', 'تم إنشاء التصنيف بنجاح');
        } catch (\Exception $e) {
            Log::error('Error in store category: ' , [
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'حدث خطأ أثناء إنشاء التصنيف. يرجى المحاولة مرة أخرى.');
        }
    }

    public function updateCategory(Request $request, Category $category)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
                'description' => 'nullable|string'
            ]);

            $category->update($validated);

            return redirect()->route('admin.categories.index')
                ->with('success', 'تم تحديث التصنيف بنجاح');
        } catch (\Exception $e) {
            Log::error('Error in update category: ' , [
                'user_id' => auth()->id(),
                'category_id' => $category->id ?? null,
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'حدث خطأ أثناء تحديث التصنيف. يرجى المحاولة مرة أخرى.');
        }
    }

    public function destroyCategory(Category $category)
    {
        try {
            if ($category->courses()->count() > 0) {
                return redirect()->back()->with('error', 'لا يمكن حذف التصنيف لوجود كورسات مرتبطة به');
            }

            $category->delete();

            return redirect()->route('admin.categories.index')
                ->with('success', 'تم حذف التصنيف بنجاح');
        } catch (\Exception $e) {
            Log::error('Error in destroy category: ' , [
                'user_id' => auth()->id(),
                'category_id' => $category->id ?? null,
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'حدث خطأ أثناء حذف التصنيف. يرجى المحاولة مرة أخرى.');
        }
    }

    // Payments Management
    public function payments()
    {
        try {
            $payments = Payment::with(['user', 'course'])
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            $totalRevenue = Payment::where('status', 'completed')->sum('amount');
            $pendingPayments = Payment::where('status', 'pending')->count();

            return view('admin.payments.index', compact('payments', 'totalRevenue', 'pendingPayments'));
        } catch (\Exception $e) {
            Log::error('Error in admin payments: ' , [
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'حدث خطأ أثناء تحميل قائمة المدفوعات. يرجى المحاولة مرة أخرى.');
        }
    }

    public function showPayment(Payment $payment)
    {
        try {
            $payment->load(['user', 'course']);
            return view('admin.payments.show', compact('payment'));
        } catch (\Exception $e) {
            Log::error('Error in show payment: ' , [
                'user_id' => auth()->id(),
                'payment_id' => $payment->id ?? null,
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'حدث خطأ أثناء عرض تفاصيل الدفع. يرجى المحاولة مرة أخرى.');
        }
    }

    // Reports
    public function reports()
    {
        try {
            // Revenue statistics
            $monthlyRevenue = Payment::where('status', 'completed')
                ->whereYear('created_at', now()->year)
                ->selectRaw('MONTH(created_at) as month, SUM(amount) as total')
                ->groupBy('month')
                ->orderBy('month')
                ->get();

            // Course statistics
            $topCourses = Course::withCount('enrollments')
                ->orderBy('enrollments_count', 'desc')
                ->take(10)
                ->get();

            // User statistics
            $userStats = [
                'total' => User::count(),
                'students' => User::where('role', 'student')->count(),
                'instructors' => User::where('role', 'instructor')->count(),
                'admins' => User::where('role', 'admin')->count()
            ];

            return view('admin.reports.index', compact('monthlyRevenue', 'topCourses', 'userStats'));
        } catch (\Exception $e) {
            Log::error('Error in admin reports: ' , [
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'حدث خطأ أثناء تحميل التقارير. يرجى المحاولة مرة أخرى.');
        }
    }

    // Settings
    public function settings()
    {
        try {
            return view('admin.settings.index');
        } catch (\Exception $e) {
            Log::error('Error in admin settings: ' , [
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'حدث خطأ أثناء تحميل صفحة الإعدادات. يرجى المحاولة مرة أخرى.');
        }
    }

    public function updateSettings(Request $request)
    {
        try {
            // Implementation for updating system settings
            return back()->with('success', 'تم تحديث الإعدادات بنجاح');
        } catch (\Exception $e) {
            Log::error('Error in update settings: ' , [
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'حدث خطأ أثناء تحديث الإعدادات. يرجى المحاولة مرة أخرى.');
        }
    }
}
