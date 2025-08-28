<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\InstructorController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Authentication Routes
Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
Route::post('login', [AuthenticatedSessionController::class, 'store']);
Auth::routes();

// General dashboard route - redirects based on user role
Route::middleware(['auth'])->get('/dashboard', function () {
    $user = auth()->user();

    if ($user->isAdmin()) {
        return redirect()->route('admin.dashboard');
    } elseif ($user->isInstructor()) {
        return redirect()->route('instructor.dashboard');
    } else {
        return redirect()->route('student.dashboard');
    }
})->name('dashboard');

// Profile routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', function () {
        return view('profile.edit');
    })->name('profile.edit');

    Route::patch('/profile', function () {
        // Handle profile update
        return redirect()->route('profile.edit')->with('status', 'profile-updated');
    })->name('profile.update');

    Route::delete('/profile', function () {
        // Handle profile deletion
        return redirect('/');
    })->name('profile.destroy');
});

// Admin Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // Users Management
    Route::get('/users', [AdminController::class, 'users'])->name('users.index');
    Route::get('/users/create', [AdminController::class, 'createUser'])->name('users.create');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
    Route::get('/users/{user}', [AdminController::class, 'showUser'])->name('users.show');
    Route::get('/users/edit/{user}', [AdminController::class, 'editUser'])->name('users.edit');
    Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
    Route::get('/users/delete/{user}', [AdminController::class, 'deleteUser'])->name('users.destroy');

    // Courses Management
    Route::get('/courses', [AdminController::class, 'courses'])->name('courses.index');
    Route::get('/courses/create', [AdminController::class, 'createCourse'])->name('courses.create');
    Route::post('/courses', [AdminController::class, 'storeCourse'])->name('courses.store');
    Route::get('/courses/{course}', [AdminController::class, 'showCourse'])->name('courses.show');
    Route::get('/courses/{course}/edit', [AdminController::class, 'editCourse'])->name('courses.edit');
    Route::put('/courses/{course}', [AdminController::class, 'updateCourse'])->name('courses.update');
    Route::delete('/courses/{course}', [AdminController::class, 'deleteCourse'])->name('courses.destroy');

    // Sections Management
    Route::get('/courses/{course}/sections', [AdminController::class, 'sections'])->name('sections.index');
    Route::get('/courses/{course}/sections/create', [AdminController::class, 'createSection'])->name('sections.create');
    Route::post('/courses/{course}/sections', [AdminController::class, 'storeSection'])->name('sections.store');
    Route::get('/sections/{section}/edit', [AdminController::class, 'editSection'])->name('sections.edit');
    Route::put('/sections/{section}', [AdminController::class, 'updateSection'])->name('sections.update');
    Route::delete('/sections/{section}', [AdminController::class, 'deleteSection'])->name('sections.destroy');

    // Lessons Management
    Route::get('/sections/{section}/lessons', [AdminController::class, 'lessons'])->name('lessons.index');
    Route::get('/sections/{section}/lessons/create', [AdminController::class, 'createLesson'])->name('lessons.create');
    Route::post('/sections/{section}/lessons', [AdminController::class, 'storeLesson'])->name('lessons.store');
    Route::get('/lessons/{lesson}', [AdminController::class, 'showLesson'])->name('lessons.show');
    Route::get('/lessons/{lesson}/edit', [AdminController::class, 'editLesson'])->name('lessons.edit');
    Route::put('/lessons/{lesson}', [AdminController::class, 'updateLesson'])->name('lessons.update');
    Route::delete('/lessons/{lesson}', [AdminController::class, 'deleteLesson'])->name('lessons.destroy');
    Route::get('/lessons/{lesson}/download', [AdminController::class, 'downloadLessonFile'])->name('lessons.download');

    // Categories Management
    Route::get('/categories', [AdminController::class, 'categories'])->name('categories.index');
    Route::post('/categories', [AdminController::class, 'storeCategory'])->name('categories.store');
    Route::put('/categories/{category}', [AdminController::class, 'updateCategory'])->name('categories.update');
    Route::delete('/categories/{category}', [AdminController::class, 'deleteCategory'])->name('categories.destroy');

    // Payments Management
    Route::get('/payments', [AdminController::class, 'payments'])->name('payments.index');
    Route::get('/payments/{payment}', [AdminController::class, 'showPayment'])->name('payments.show');

    // Reports
    Route::get('/reports', [AdminController::class, 'reports'])->name('reports.index');

    // Settings
    Route::get('/settings', [AdminController::class, 'settings'])->name('settings.index');
    Route::put('/settings', [AdminController::class, 'updateSettings'])->name('settings.update');
});

// Student Routes
Route::middleware(['auth', 'student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [StudentController::class, 'dashboard'])->name('dashboard');
    Route::get('/courses', [StudentController::class, 'courses'])->name('courses.index');
    Route::get('/courses/{course}', [StudentController::class, 'showCourse'])->name('courses.show');
    Route::get('/courses/{course}/learn', [StudentController::class, 'learnCourse'])->name('courses.learn');
    Route::post('/courses/{course}/enroll', [StudentController::class, 'enrollCourse'])->name('courses.enroll');
    Route::get('/enrolled-courses', [StudentController::class, 'enrolledCourses'])->name('enrolled-courses.index');
    Route::get('/lessons/{lesson}/download', [StudentController::class, 'downloadLessonFile'])->name('lessons.download');

    // Lesson progress routes
    Route::get('/lessons/{lesson}/progress', [StudentController::class, 'getLessonProgress'])->name('lessons.progress');
    Route::post('/lessons/{lesson}/progress', [StudentController::class, 'updateLessonProgress'])->name('lessons.progress.update');
    Route::post('/lessons/{lesson}/complete', [StudentController::class, 'completeLesson'])->name('lessons.complete');
    Route::post('/lessons/{lesson}/next', [StudentController::class, 'moveToNextLesson'])->name('lessons.next');

    // Secure video streaming route
    Route::get('/secure-video/{lesson}', function(App\Models\Lesson $lesson) {
        // Check if user has access to this lesson
        $user = auth()->user();
        if (!$user) {
            abort(403, 'Unauthorized');
        }

        // Check if user is enrolled or has section access
        $enrollment = $user->enrollments()
            ->where('course_id', $lesson->section->course_id)
            ->first();

        $hasSectionAccess = $user->sectionAccess()
            ->active()
            ->where('course_id', $lesson->section->course_id)
            ->exists();

        if (!$enrollment && !$hasSectionAccess) {
            abort(403, 'Access denied');
        }

        // Check if lesson has video file
        if (!$lesson->file_path || !Storage::disk('public')->exists($lesson->file_path)) {
            abort(404, 'Video not found');
        }

        $filePath = Storage::disk('public')->path($lesson->file_path);
        $mimeType = $lesson->mime_type ?? 'video/mp4';

        // Set security headers
        $headers = [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . basename($lesson->file_path) . '"',
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'DENY',
            'X-XSS-Protection' => '1; mode=block',
            'Referrer-Policy' => 'strict-origin-when-cross-origin',
            'Cache-Control' => 'no-cache, no-store, must-revalidate, private',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ];

        // Stream video with security headers
        return response()->file($filePath, $headers);
    })->name('secure.video')->middleware('throttle:60,1'); // Rate limiting

    // Danger page route for security violations
    Route::get('/danger-page', function() {
        $user = auth()->user();

        // Log security violation
        \Log::warning('Security violation detected', [
            'user_id' => $user->id ?? 'unknown',
            'user_email' => $user->email ?? 'unknown',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now(),
            'violation_type' => 'developer_tools_opened',
            'page' => request()->headers->get('referer')
        ]);

        return view('security.danger-page', compact('user'));
    })->name('danger.page');
});

// Instructor Routes
Route::middleware(['auth', 'instructor'])->prefix('instructor')->name('instructor.')->group(function () {
    Route::get('/dashboard', [InstructorController::class, 'dashboard'])->name('dashboard');
    Route::get('/courses', [InstructorController::class, 'courses'])->name('courses.index');
    Route::get('/courses/create', [InstructorController::class, 'createCourse'])->name('courses.create');
    Route::post('/courses', [InstructorController::class, 'storeCourse'])->name('courses.store');
    Route::get('/courses/{course}', [InstructorController::class, 'showCourse'])->name('courses.show');
    Route::get('/courses/{course}/edit', [InstructorController::class, 'editCourse'])->name('courses.edit');
    Route::put('/courses/{course}', [InstructorController::class, 'updateCourse'])->name('courses.update');
    Route::delete('/courses/{course}', [InstructorController::class, 'deleteCourse'])->name('courses.destroy');

    // Students Management
    Route::get('/students', [InstructorController::class, 'students'])->name('students.index');
    Route::get('/students/{student}', [InstructorController::class, 'showStudent'])->name('students.show');

    // Earnings
    Route::get('/earnings', [InstructorController::class, 'earnings'])->name('earnings.index');
});

// Public Course Routes
Route::get('/courses', [StudentController::class, 'courses'])->name('student.courses.index');
Route::get('/courses/{course}', [StudentController::class, 'showCourse'])->name('student.courses.show');

// Payment Routes
Route::middleware('auth')->group(function () {
    Route::get('/payment/{course}/checkout', [PaymentController::class, 'checkout'])->name('payment.checkout');
    Route::get('/payment/{course}/section/{section}/checkout', [PaymentController::class, 'checkout'])->name('payment.section.checkout');
    Route::post('/payment/{course}/process', [PaymentController::class, 'process'])->name('payment.process');
    Route::post('/payment/{course}/section/{section}/process', [PaymentController::class, 'process'])->name('payment.section.process');
    Route::post('/payment/confirm-stripe', [PaymentController::class, 'confirmStripePayment'])->name('payment.confirm.stripe');
    Route::get('/payment/success', [PaymentController::class, 'success'])->name('payment.success');
    Route::get('/payment/cancel', [PaymentController::class, 'cancel'])->name('payment.cancel');
});

// Payment Webhooks (no auth required)
Route::post('/payment/webhook/{gateway}', [PaymentController::class, 'webhook'])->name('payment.webhook');

// Test payment route (remove in production)
Route::get('/test-payment', function() {
    return view('payment.test');
})->name('payment.test');

// Debug payment table structure (remove in production)
Route::get('/debug-payment-table', function() {
    try {
        $columns = \DB::select('DESCRIBE payments');
        $fillable = (new \App\Models\Payment())->getFillable();

        return response()->json([
            'success' => true,
            'table_columns' => $columns,
            'model_fillable' => $fillable,
            'migration_file' => file_get_contents(database_path('migrations/2025_07_23_185800_create_payments_table.php'))
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }
})->name('debug.payment.table');

// Section Access Routes (with middleware)
Route::middleware(['auth', 'section.access'])->group(function () {
    Route::get('/sections/{section}', function ($section) {
        return redirect()->route('student.courses.show', $section->course_id);
    })->name('sections.show');

    Route::get('/lessons/{lesson}', function ($lesson) {
        return redirect()->route('student.courses.learn', $lesson->section->course_id, ['lesson' => $lesson->id]);
    })->name('lessons.show');
});

