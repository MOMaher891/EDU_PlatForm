<?php

namespace App\Services;

use App\Models\Course;
use App\Models\CourseSection;
use App\Models\StudentSectionAccess;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class SectionAccessService
{
    /**
     * Grant access to a section for a user
     */
    public function grantAccess(User $user, CourseSection $section, $paymentId = null, $pricePaid = null)
    {
        return DB::transaction(function () use ($user, $section, $paymentId, $pricePaid) {
            // Check if user already has access
            $existingAccess = StudentSectionAccess::where('user_id', $user->id)
                ->where('section_id', $section->id)
                ->first();

            if ($existingAccess) {
                // Update existing access
                $existingAccess->update([
                    'is_active' => true,
                    'access_granted_at' => now(),
                    'payment_id' => $paymentId,
                    'price_paid' => $pricePaid ?? $section->getEffectivePrice() ?? 0
                ]);
                return $existingAccess;
            }

            // Create new access record
            return StudentSectionAccess::create([
                'user_id' => $user->id,
                'course_id' => $section->course_id,
                'section_id' => $section->id,
                'payment_id' => $paymentId,
                'price_paid' => $pricePaid ?? $section->getEffectivePrice() ?? 0,
                'access_granted_at' => now(),
                'is_active' => true
            ]);
        });
    }

    /**
     * Check if user has access to a specific section
     */
    public function hasAccess(User $user, CourseSection $section): bool
    {
        // Check if user is enrolled in the full course
        $hasFullCourseAccess = $user->enrollments()
            ->where('course_id', $section->course_id)
            ->exists();

        if ($hasFullCourseAccess) {
            return true;
        }

        // Check for section-specific access
        return $user->hasSectionAccess($section->id);
    }

    /**
     * Check if user has access to a specific lesson
     */
    public function hasLessonAccess(User $user, $lessonId): bool
    {
        $lesson = \App\Models\Lesson::find($lessonId);

        if (!$lesson) {
            return false;
        }

        return $this->hasAccess($user, $lesson->section);
    }

    /**
     * Get all accessible sections for a user in a course
     */
    public function getAccessibleSections(User $user, Course $course)
    {
        // Get sections from full course enrollment
        $enrolledSections = $user->enrollments()
            ->where('course_id', $course->id)
            ->exists() ? $course->sections()->with(['lessons' => function($query) {
                $query->orderBy('order_index');
            }])->get() : collect();

        // Get sections from individual access
        $individualSections = $user->sectionAccess()
            ->active()
            ->where('course_id', $course->id)
            ->with(['section.lessons' => function($query) {
                $query->orderBy('order_index');
            }])
            ->get()
            ->pluck('section');

        // Merge and return unique sections
        return $enrolledSections->merge($individualSections)->unique('id');
    }

    /**
     * Revoke access to a section for a user
     */
    public function revokeAccess(User $user, CourseSection $section)
    {
        return StudentSectionAccess::where('user_id', $user->id)
            ->where('section_id', $section->id)
            ->update(['is_active' => false]);
    }

    /**
     * Get section access statistics
     */
    public function getAccessStatistics(CourseSection $section)
    {
        return [
            'total_access' => $section->studentAccess()->count(),
            'active_access' => $section->studentAccess()->active()->count(),
            'total_revenue' => $section->studentAccess()->sum('price_paid'),
        ];
    }
}
