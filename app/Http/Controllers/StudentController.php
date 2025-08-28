<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Category;
use App\Models\CourseEnrollment;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\User;
use App\Models\Activity;
use App\Services\SectionAccessService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class StudentController extends Controller
{
    protected $sectionAccessService;

    public function __construct(SectionAccessService $sectionAccessService)
    {
        $this->middleware('auth');
        $this->sectionAccessService = $sectionAccessService;
    }

    public function dashboard()
    {
        try {
            $user = Auth::user();

            // Get student's enrolled courses
            $enrolledCourses = $user->enrollments()->with(['course.instructor', 'course.category'])->get();

            // Get accessible sections
            $accessibleSections = $user->getAccessibleSections();

            // Calculate statistics
            $totalCourses = $enrolledCourses->count();
            $completedCourses = $enrolledCourses->where('progress', 100)->count();
            $inProgressCourses = $enrolledCourses->where('progress', '>', 0)->where('progress', '<', 100);
            $totalHours = $enrolledCourses->sum(function($enrollment) {
                return $enrollment->course->duration_hours;
            });

            // Get recent activities
            $recentActivities = $user->activities()
                ->with('course')
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get();

            // Get recommended courses
            $recommendedCourses = Course::where('is_published', true)
                ->whereNotIn('id', $enrolledCourses->pluck('course_id'))
                ->with(['instructor', 'category'])
                ->inRandomOrder()
                ->take(6)
                ->get();

            return view('student.dashboard', compact(
                'enrolledCourses',
                'accessibleSections',
                'totalCourses',
                'completedCourses',
                'inProgressCourses',
                'totalHours',
                'recentActivities',
                'recommendedCourses'
            ));
        } catch (\Exception $e) {
            Log::error('Error in student dashboard: ' , [
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'حدث خطأ أثناء تحميل لوحة التحكم. يرجى المحاولة مرة أخرى.');
        }
    }

    public function courses()
    {
        try {
            $user = Auth::user();

            // Get all categories for filtering
            $categories = Category::all();

            // Build query for courses
            $query = Course::where('is_published', true)
                ->with(['instructor', 'category']);

            // Apply search filter
            if (request('search')) {
                $query->where(function($q) {
                    $q->where('title', 'like', '%' . request('search') . '%')
                      ->orWhere('description', 'like', '%' . request('search') . '%')
                      ->orWhere('short_description', 'like', '%' . request('search') . '%');
                });
            }

            // Apply category filter
            if (request('category')) {
                $query->where('category_id', request('category'));
            }

            // Apply level filter
            if (request('level')) {
                $query->where('level', request('level'));
            }

            // Apply price filter
            if (request('price')) {
                switch (request('price')) {
                    case 'free':
                        $query->where('price', 0);
                        break;
                    case '0-50':
                        $query->whereBetween('price', [0, 50]);
                        break;
                    case '50-100':
                        $query->whereBetween('price', [50, 100]);
                        break;
                    case '100+':
                        $query->where('price', '>', 100);
                        break;
                }
            }

            // Apply sorting
            switch (request('sort', 'created_at')) {
                case 'title':
                    $query->orderBy('title');
                    break;
                case 'price':
                    $query->orderBy('price');
                    break;
                case 'popular':
                    $query->orderBy('enrollments_count', 'desc');
                    break;
                default:
                    $query->orderBy('created_at', 'desc');
                    break;
            }

            // Get paginated courses
            $courses = $query->paginate(12);

            return view('student.courses.index', compact(
                'courses',
                'categories'
            ));
        } catch (\Exception $e) {
            Log::error('Error in student courses: ' , [
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'حدث خطأ أثناء تحميل الكورسات. يرجى المحاولة مرة أخرى.');
        }
    }

    public function enrolledCourses()
    {
        try {
            $user = Auth::user();

            // Get all enrolled courses with relationships
            $query = $user->enrollments()
                ->with(['course.instructor', 'course.category']);

            // Apply status filter
            if (request('status')) {
                switch (request('status')) {
                    case 'completed':
                        $query->where('progress', 100);
                        break;
                    case 'in_progress':
                        $query->where('progress', '>', 0)->where('progress', '<', 100);
                        break;
                    case 'not_started':
                        $query->where('progress', 0);
                        break;
                }
            }

            // Apply search filter
            if (request('search')) {
                $query->whereHas('course', function($q) {
                    $q->where('title', 'like', '%' . request('search') . '%')
                      ->orWhere('description', 'like', '%' . request('search') . '%');
                });
            }

            // Apply category filter
            if (request('category')) {
                $query->whereHas('course', function($q) {
                    $q->where('category_id', request('category'));
                });
            }

            // Apply sorting
            switch (request('sort', 'recent')) {
                case 'title':
                    $query->whereHas('course', function($q) {
                        $q->orderBy('title');
                    });
                    break;
                case 'progress':
                    $query->orderBy('progress', 'desc');
                    break;
                case 'recent':
                    $query->orderBy('updated_at', 'desc');
                    break;
                case 'oldest':
                    $query->orderBy('created_at', 'asc');
                    break;
            }

            // Get paginated enrollments
            $enrollments = $query->paginate(12);

            // Get categories for filtering
            $categories = Category::all();

            // Calculate statistics
            $totalEnrolled = $user->enrollments()->count();
            $completedCourses = $user->enrollments()->where('progress', 100)->count();
            $inProgressCourses = $user->enrollments()->where('progress', '>', 0)->where('progress', '<', 100)->count();
            $notStartedCourses = $user->enrollments()->where('progress', 0)->count();

            return view('student.enrolled-courses.index', compact(
                'enrollments',
                'categories',
                'totalEnrolled',
                'completedCourses',
                'inProgressCourses',
                'notStartedCourses'
            ));
        } catch (\Exception $e) {
            Log::error('Error in enrolled courses: ' , [
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'حدث خطأ أثناء تحميل الكورسات المسجلة. يرجى المحاولة مرة أخرى.');
        }
    }

    public function showCourse(Course $course)
    {
        try {
            $user = Auth::user();

            // Check enrollment status
            $enrollment = $user->enrollments()
                ->where('course_id', $course->id)
                ->first();

            $isEnrolled = $enrollment !== null;

            // Check section access
            $accessibleSections = $user->sectionAccess()
                ->active()
                ->where('course_id', $course->id)
                ->with('section')
                ->get();

            $hasSectionAccess = $accessibleSections->count() > 0;

            // Load course data
            $course->load(['sections.lessons' => function($query) {
                $query->orderBy('order_index');
            }, 'instructor', 'category']);

            // Calculate total lessons
            $totalLessons = $course->getTotalLessons();

            // Get course reviews/ratings
            $reviews = $course->reviews()->with('user')->latest()->take(10)->get();
            $averageRating = $course->reviews()->avg('rating') ?? 0;
            $totalReviews = $course->reviews()->count();

            return view('student.courses.show', compact(
                'course',
                'isEnrolled',
                'enrollment',
                'accessibleSections',
                'hasSectionAccess',
                'totalLessons',
                'reviews',
                'averageRating',
                'totalReviews'
            ));
        } catch (\Exception $e) {
            Log::error('Error in show course: ' , [
                'user_id' => Auth::id(),
                'course_id' => $course->id ?? null,
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'حدث خطأ أثناء تحميل تفاصيل الكورس. يرجى المحاولة مرة أخرى.');
        }
    }

    public function learnCourse(Course $course)
    {
        try {
            $user = Auth::user();

            // Check if user is enrolled or has section access
            $enrollment = $user->enrollments()
                ->where('course_id', $course->id)
                ->first();

            $hasSectionAccess = $user->sectionAccess()
                ->active()
                ->where('course_id', $course->id)
                ->exists();

            if (!$enrollment && !$hasSectionAccess) {
                return redirect()->route('student.courses.show', $course)
                    ->with('error', 'يجب التسجيل في الكورس أولاً أو شراء قسم منفصل');
            }

            $course->load(['sections.lessons' => function($query) {
                $query->orderBy('order_index');
            }]);

            // Get accessible sections
            $accessibleSections = $this->sectionAccessService->getAccessibleSections($user, $course);

            // Get current lesson or first accessible lesson
            $currentLesson = null;

            if (request('lesson')) {
                $currentLesson = Lesson::findOrFail(request('lesson'));

                // Check if user has access to this lesson's section
                if (!$this->sectionAccessService->hasLessonAccess($user, $currentLesson->id)) {
                    return redirect()->route('student.courses.show', $course)
                        ->with('error', 'You do not have access to this lesson.');
                }
            } else {
                // Get first accessible lesson
                foreach ($accessibleSections as $section) {
                    if ($section->lessons->count() > 0) {
                        $currentLesson = $section->lessons->first();
                        break;
                    }
                }
            }

            // If no lessons are available, redirect with a message
            if (!$currentLesson) {
                return redirect()->route('student.courses.show', $course)
                    ->with('error', 'لا توجد دروس متاحة في هذا الكورس حالياً');
            }

            // Get user's lesson progress
            $lessonProgress = $user->lessonProgress()
                ->where('course_id', $course->id)
                ->pluck('is_completed', 'lesson_id')
                ->toArray();

            // Get lesson watch times
            $lessonWatchTimes = $user->lessonProgress()
                ->where('course_id', $course->id)
                ->pluck('watch_time', 'lesson_id')
                ->toArray();

            // Get next and previous lessons
            $nextLesson = $this->getNextLesson($currentLesson, $accessibleSections);
            $prevLesson = $this->getPreviousLesson($currentLesson, $accessibleSections);

            return view('student.courses.learn', compact(
                'course',
                'currentLesson',
                'enrollment',
                'accessibleSections',
                'lessonProgress',
                'lessonWatchTimes',
                'nextLesson',
                'prevLesson'
            ));
        } catch (\Exception $e) {
            Log::error('Error in learn course: ' , [
                'user_id' => Auth::id(),
                'course_id' => $course->id ?? null,
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'حدث خطأ أثناء تحميل صفحة التعلم. يرجى المحاولة مرة أخرى.');
        }
    }

    public function enrollCourse(Course $course)
    {
        try {
            $user = Auth::user();

            // Check if already enrolled
            if ($user->enrollments()->where('course_id', $course->id)->exists()) {
                return redirect()->route('student.courses.show', $course)
                    ->with('info', 'أنت مسجل في هذا الكورس بالفعل');
            }

            // Create enrollment
            $enrollment = CourseEnrollment::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'enrolled_at' => now(),
                'progress' => 0
            ]);

            // Log activity
            Activity::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'type' => 'enrollment',
                'description' => 'تم التسجيل في الكورس: ' . $course->title
            ]);

            return redirect()->route('student.courses.learn', $course)
                ->with('success', 'تم التسجيل في الكورس بنجاح');
        } catch (\Exception $e) {
            Log::error('Error in enroll course: ' , [
                'user_id' => Auth::id(),
                'course_id' => $course->id ?? null,
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'حدث خطأ أثناء التسجيل في الكورس. يرجى المحاولة مرة أخرى.');
        }
    }

    public function updateLessonProgress(Request $request, Lesson $lesson)
    {
        try {
            $user = Auth::user();

            // Check access to lesson
            if (!$this->sectionAccessService->hasLessonAccess($user, $lesson->id)) {
                return response()->json(['error' => 'No access to this lesson'], 403);
            }

            $data = $request->validate([
                'is_completed' => 'boolean',
                'watch_time' => 'integer|min:0',
                'completed_at' => 'nullable|date'
            ]);

            // Get or create lesson progress
            $progress = LessonProgress::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'course_id' => $lesson->section->course_id,
                    'lesson_id' => $lesson->id
                ],
                [
                    'is_completed' => $data['is_completed'] ?? false,
                    'watch_time' => $data['watch_time'] ?? 0,
                    'completed_at' => $data['is_completed'] ? now() : null
                ]
            );

            // Update course enrollment progress
            $this->updateCourseProgress($user, $lesson->section->course_id);

            // Log activity if lesson completed
            if ($data['is_completed'] ?? false) {
                Activity::create([
                    'user_id' => $user->id,
                    'course_id' => $lesson->section->course_id,
                    'type' => 'lesson_completed',
                    'description' => 'تم إكمال الدرس: ' . $lesson->title
                ]);
            }

            return response()->json([
                'success' => true,
                'progress' => $progress,
                'course_progress' => $user->enrollments()->where('course_id', $lesson->section->course_id)->first()->progress ?? 0
            ]);
        } catch (\Exception $e) {
            Log::error('Error in update lesson progress: ' , [
                'user_id' => Auth::id(),
                'lesson_id' => $lesson->id ?? null,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'حدث خطأ أثناء تحديث تقدم الدرس. يرجى المحاولة مرة أخرى.'
            ], 500);
        }
    }

    public function completeLesson(Request $request, Lesson $lesson)
    {
        try {
            $user = Auth::user();

            // Check access to lesson
            if (!$this->sectionAccessService->hasLessonAccess($user, $lesson->id)) {
                return response()->json(['error' => 'No access to this lesson'], 403);
            }

            // Mark lesson as completed
            $progress = LessonProgress::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'course_id' => $lesson->section->course_id,
                    'lesson_id' => $lesson->id
                ],
                [
                    'is_completed' => true,
                    'watch_time' => $request->input('watch_time', 0),
                    'completed_at' => now()
                ]
            );

            // Update course enrollment progress
            $this->updateCourseProgress($user, $lesson->section->course_id);

            // Log activity
            Activity::create([
                'user_id' => $user->id,
                'course_id' => $lesson->section->course_id,
                'type' => 'lesson_completed',
                'description' => 'تم إكمال الدرس: ' . $lesson->title
            ]);

            // Get next lesson information
            $course = $lesson->section->course;
            $accessibleSections = $this->sectionAccessService->getAccessibleSections($user, $course);
            $nextLesson = $this->getNextLesson($lesson, $accessibleSections);

            return response()->json([
                'success' => true,
                'progress' => $progress,
                'course_progress' => $user->enrollments()->where('course_id', $lesson->section->course_id)->first()->progress ?? 0,
                'next_lesson' => $nextLesson ? [
                    'id' => $nextLesson->id,
                    'title' => $nextLesson->title,
                    'url' => route('student.courses.learn', $course, ['lesson' => $nextLesson->id])
                ] : null,
                'is_course_completed' => $this->isCourseCompleted($user, $course)
            ]);
        } catch (\Exception $e) {
            Log::error('Error in complete lesson: ' , [
                'user_id' => Auth::id(),
                'lesson_id' => $lesson->id ?? null,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'حدث خطأ أثناء إكمال الدرس. يرجى المحاولة مرة أخرى.'
            ], 500);
        }
    }

    public function moveToNextLesson(Request $request, Lesson $lesson)
    {
        try {
            $user = Auth::user();

            // Check access to lesson
            if (!$this->sectionAccessService->hasLessonAccess($user, $lesson->id)) {
                return response()->json(['error' => 'No access to this lesson'], 403);
            }

            $course = $lesson->section->course;
            $accessibleSections = $this->sectionAccessService->getAccessibleSections($user, $course);
            $nextLesson = $this->getNextLesson($lesson, $accessibleSections);

            if (!$nextLesson) {
                return response()->json([
                    'success' => false,
                    'message' => 'لا توجد دروس أخرى متاحة',
                    'is_course_completed' => $this->isCourseCompleted($user, $course)
                ]);
            }

            return response()->json([
                'success' => true,
                'next_lesson' => [
                    'id' => $nextLesson->id,
                    'title' => $nextLesson->title,
                    'url' => route('student.courses.learn', $course, ['lesson' => $nextLesson->id])
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error in move to next lesson: ' , [
                'user_id' => Auth::id(),
                'lesson_id' => $lesson->id ?? null,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'حدث خطأ أثناء الانتقال للدرس التالي. يرجى المحاولة مرة أخرى.'
            ], 500);
        }
    }

    public function getLessonProgress(Request $request, Lesson $lesson)
    {
        try {
            $user = Auth::user();

            $progress = LessonProgress::where([
                'user_id' => $user->id,
                'course_id' => $lesson->section->course_id,
                'lesson_id' => $lesson->id
            ])->first();

            return response()->json([
                'progress' => $progress ? [
                    'is_completed' => $progress->is_completed,
                    'watch_time' => $progress->watch_time,
                    'completed_at' => $progress->completed_at
                ] : null
            ]);
        } catch (\Exception $e) {
            Log::error('Error in get lesson progress: ' , [
                'user_id' => Auth::id(),
                'lesson_id' => $lesson->id ?? null,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'حدث خطأ أثناء جلب تقدم الدرس. يرجى المحاولة مرة أخرى.'
            ], 500);
        }
    }

    public function downloadLessonFile(Lesson $lesson)
    {
        try {
            $user = Auth::user();

            // Check access to lesson
            if (!$this->sectionAccessService->hasLessonAccess($user, $lesson->id)) {
                return redirect()->back()->with('error', 'You do not have access to this lesson.');
            }

            if (!$lesson->hasFile()) {
                return redirect()->back()->with('error', 'No file available for download.');
            }

            $filePath = storage_path('app/public/' . $lesson->file_path);

            if (!file_exists($filePath)) {
                return redirect()->back()->with('error', 'File not found.');
            }

            return response()->download($filePath, $lesson->file_name ?? basename($lesson->file_path));
        } catch (\Exception $e) {
            Log::error('Error in download lesson file: ' , [
                'user_id' => Auth::id(),
                'lesson_id' => $lesson->id ?? null,
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'حدث خطأ أثناء تحميل الملف. يرجى المحاولة مرة أخرى.');
        }
    }

    public function getNextLesson(?Lesson $lesson, $accessibleSections)
    {
        try {
            if (!$lesson) {
                return null;
            }

            $currentSection = $lesson->section;

            // Debug: Check if lessons are loaded
            if (!$currentSection->relationLoaded('lessons')) {
                $currentSection->load(['lessons' => function($query) {
                    $query->orderBy('order_index');
                }]);
            }

            $currentIndex = $currentSection->lessons->search(function($item) use ($lesson) {
                return $item->id === $lesson->id;
            });

            // Check if there's a next lesson in the same section
            if ($currentIndex !== false && $currentIndex < $currentSection->lessons->count() - 1) {
                return $currentSection->lessons[$currentIndex + 1];
            }

            // Check next sections
            $currentSectionIndex = $accessibleSections->search($currentSection);
            if ($currentSectionIndex !== false && $currentSectionIndex < $accessibleSections->count() - 1) {
                $nextSection = $accessibleSections[$currentSectionIndex + 1];

                // Debug: Check if lessons are loaded for next section
                if (!$nextSection->relationLoaded('lessons')) {
                    $nextSection->load(['lessons' => function($query) {
                        $query->orderBy('order_index');
                    }]);
                }

                if ($nextSection->lessons->count() > 0) {
                    return $nextSection->lessons->first();
                }
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Error in get next lesson: ' , [
                'lesson_id' => $lesson->id ?? null,
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }

    public function getPreviousLesson(?Lesson $lesson, $accessibleSections)
    {
        try {
            if (!$lesson) {
                return null;
            }

            $currentSection = $lesson->section;
            $currentIndex = $currentSection->lessons->search($lesson);

            // Check if there's a previous lesson in the same section
            if ($currentIndex !== false && $currentIndex > 0) {
                return $currentSection->lessons[$currentIndex - 1];
            }

            // Check previous sections
            $currentSectionIndex = $accessibleSections->search($currentSection);
            if ($currentSectionIndex !== false && $currentSectionIndex > 0) {
                $prevSection = $accessibleSections[$currentSectionIndex - 1];
                if ($prevSection->lessons->count() > 0) {
                    return $prevSection->lessons->last();
                }
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Error in get previous lesson: ' , [
                'lesson_id' => $lesson->id ?? null,
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }

    private function updateCourseProgress(User $user, $courseId)
    {
        try {
            $enrollment = $user->enrollments()->where('course_id', $courseId)->first();

            if (!$enrollment) {
                return;
            }

            $course = Course::find($courseId);
            $totalLessons = $course->getTotalLessons();

            if ($totalLessons === 0) {
                return;
            }

            $completedLessons = $user->lessonProgress()
                ->where('course_id', $courseId)
                ->where('is_completed', true)
                ->count();

            $progress = ($completedLessons / $totalLessons) * 100;

            $enrollment->update([
                'progress' => $progress,
                'completed_at' => $progress >= 100 ? now() : null
            ]);

            // Log course completion
            if ($progress >= 100 && !$enrollment->completed_at) {
                Activity::create([
                    'user_id' => $user->id,
                    'course_id' => $courseId,
                    'type' => 'course_completed',
                    'description' => 'تم إكمال الكورس: ' . $course->title
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error in update course progress: ' , [
                'user_id' => $user->id ?? null,
                'course_id' => $courseId,
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    private function isCourseCompleted(User $user, Course $course)
    {
        try {
            $enrollment = $user->enrollments()->where('course_id', $course->id)->first();
            return $enrollment && $enrollment->completed_at !== null;
        } catch (\Exception $e) {
            Log::error('Error in is course completed: ' , [
                'user_id' => $user->id ?? null,
                'course_id' => $course->id ?? null,
                'trace' => $e->getTraceAsString()
            ]);

            return false;
        }
    }
}
