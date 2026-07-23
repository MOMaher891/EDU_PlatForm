<?php

namespace App\Http\Middleware;

use App\Models\CourseSection;
use App\Models\Lesson;
use App\Services\SectionAccessService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SectionAccessMiddleware
{
    protected $sectionAccessService;

    public function __construct(SectionAccessService $sectionAccessService)
    {
        $this->sectionAccessService = $sectionAccessService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        // Allow admins and instructors to access everything
        if ($user->isAdmin() || $user->isInstructor()) {
            return $next($request);
        }

        // Check for section access
        if ($request->route('section')) {
            $section = $request->route('section');

            if (!$this->sectionAccessService->hasAccess($user, $section)) {
                return redirect()->route('student.courses.show', $section->course_id)
                    ->with('error', 'You do not have access to this section. Please purchase access to continue.');
            }
        }

        // Check for lesson access
        if ($request->route('lesson')) {
            $lesson = $request->route('lesson');

            if (!$this->sectionAccessService->hasLessonAccess($user, $lesson->id)) {
                return redirect()->route('student.courses.show', $lesson->section->course_id)
                    ->with('error', 'You do not have access to this lesson. Please purchase access to continue.');
            }
        }

        return $next($request);
    }
}
