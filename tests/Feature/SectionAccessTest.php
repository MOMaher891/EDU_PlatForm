<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Course;
use App\Models\CourseSection;
use App\Models\StudentSectionAccess;
use App\Services\SectionAccessService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SectionAccessTest extends TestCase
{
    use RefreshDatabase;

    protected $sectionAccessService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sectionAccessService = app(SectionAccessService::class);
    }

    public function test_user_can_purchase_section_access()
    {
        $user = User::factory()->create(['role' => 'student']);
        $course = Course::factory()->create();
        $section = CourseSection::factory()->create([
            'course_id' => $course->id,
            'price' => 25.00,
            'is_purchasable_separately' => true
        ]);

        // Grant access to section
        $access = $this->sectionAccessService->grantAccess($user, $section, null, 25.00);

        $this->assertDatabaseHas('student_section_access', [
            'user_id' => $user->id,
            'section_id' => $section->id,
            'course_id' => $course->id,
            'price_paid' => 25.00,
            'is_active' => true
        ]);

        $this->assertTrue($this->sectionAccessService->hasAccess($user, $section));
    }

    public function test_user_with_full_course_access_can_access_all_sections()
    {
        $user = User::factory()->create(['role' => 'student']);
        $course = Course::factory()->create();
        $section = CourseSection::factory()->create(['course_id' => $course->id]);

        // Create full course enrollment
        $user->enrollments()->create([
            'course_id' => $course->id,
            'enrolled_at' => now(),
            'progress' => 0
        ]);

        $this->assertTrue($this->sectionAccessService->hasAccess($user, $section));
    }

    public function test_user_without_access_cannot_access_section()
    {
        $user = User::factory()->create(['role' => 'student']);
        $course = Course::factory()->create();
        $section = CourseSection::factory()->create(['course_id' => $course->id]);

        $this->assertFalse($this->sectionAccessService->hasAccess($user, $section));
    }

    public function test_section_access_can_be_revoked()
    {
        $user = User::factory()->create(['role' => 'student']);
        $course = Course::factory()->create();
        $section = CourseSection::factory()->create(['course_id' => $course->id]);

        // Grant access
        $this->sectionAccessService->grantAccess($user, $section);
        $this->assertTrue($this->sectionAccessService->hasAccess($user, $section));

        // Revoke access
        $this->sectionAccessService->revokeAccess($user, $section);
        $this->assertFalse($this->sectionAccessService->hasAccess($user, $section));
    }

        public function test_get_accessible_sections_for_user()
    {
        $user = User::factory()->create(['role' => 'student']);
        $course = Course::factory()->create();

        $section1 = CourseSection::factory()->create(['course_id' => $course->id]);
        $section2 = CourseSection::factory()->create(['course_id' => $course->id]);
        $section3 = CourseSection::factory()->create(['course_id' => $course->id]);

        // Grant access to sections 1 and 2
        $this->sectionAccessService->grantAccess($user, $section1);
        $this->sectionAccessService->grantAccess($user, $section2);

        $accessibleSections = $this->sectionAccessService->getAccessibleSections($user, $course);

        $this->assertCount(2, $accessibleSections);
        $this->assertTrue($accessibleSections->contains('id', $section1->id));
        $this->assertTrue($accessibleSections->contains('id', $section2->id));
        $this->assertFalse($accessibleSections->contains('id', $section3->id));
    }

    public function test_learn_method_handles_course_with_no_lessons()
    {
        $user = User::factory()->create(['role' => 'student']);
        $course = Course::factory()->create();

        // Create a section but no lessons
        $section = CourseSection::factory()->create(['course_id' => $course->id]);

        // Enroll user in course
        $user->enrollments()->create([
            'course_id' => $course->id,
            'enrolled_at' => now(),
            'progress' => 0
        ]);

        // Try to access the learn page
        $response = $this->actingAs($user)
            ->get(route('student.courses.learn', $course));

        // Should redirect with error message
        $response->assertRedirect(route('student.courses.show', $course));
        $response->assertSessionHas('error', 'لا توجد دروس متاحة في هذا الكورس حالياً');
    }

    public function test_lesson_completion_and_progression()
    {
        $user = User::factory()->create(['role' => 'student']);
        $course = Course::factory()->create();

        // Create sections and lessons
        $section1 = CourseSection::factory()->create(['course_id' => $course->id, 'order_index' => 1]);
        $section2 = CourseSection::factory()->create(['course_id' => $course->id, 'order_index' => 2]);

                $lesson1 = \App\Models\Lesson::factory()->create([
            'section_id' => $section1->id,
            'order_index' => 1,
            'title' => 'Lesson 1',
            'is_active' => true
        ]);

        $lesson2 = \App\Models\Lesson::factory()->create([
            'section_id' => $section1->id,
            'order_index' => 2,
            'title' => 'Lesson 2',
            'is_active' => true
        ]);

        // Enroll user in course
        $user->enrollments()->create([
            'course_id' => $course->id,
            'enrolled_at' => now(),
            'progress' => 0
        ]);

                        // Test lesson completion
        $response = $this->actingAs($user)
            ->postJson(route('student.lessons.complete', $lesson1), [
                'watch_time' => 300
            ]);

        $response->assertStatus(200);

        $response->assertJson([
            'success' => true,
            'next_lesson' => [
                'id' => $lesson2->id,
                'title' => 'Lesson 2'
            ]
        ]);

        // Verify lesson progress was created
        $this->assertDatabaseHas('lesson_progress', [
            'user_id' => $user->id,
            'course_id' => $course->id,
            'lesson_id' => $lesson1->id,
            'is_completed' => true
        ]);

        // Test moving to next lesson
        $response = $this->actingAs($user)
            ->postJson(route('student.lessons.next', $lesson1));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'next_lesson' => [
                'id' => $lesson2->id,
                'title' => 'Lesson 2'
            ]
        ]);
    }

    public function test_lesson_navigation_works_correctly()
    {
        $user = User::factory()->create(['role' => 'student']);
        $course = Course::factory()->create();

        // Create sections and lessons
        $section1 = CourseSection::factory()->create(['course_id' => $course->id, 'order_index' => 1]);

        $lesson1 = \App\Models\Lesson::factory()->create([
            'section_id' => $section1->id,
            'order_index' => 1,
            'title' => 'Lesson 1',
            'is_active' => true
        ]);

        $lesson2 = \App\Models\Lesson::factory()->create([
            'section_id' => $section1->id,
            'order_index' => 2,
            'title' => 'Lesson 2',
            'is_active' => true
        ]);

        // Enroll user in course
        $user->enrollments()->create([
            'course_id' => $course->id,
            'enrolled_at' => now(),
            'progress' => 0
        ]);

        // Test navigation to lesson 1
        $response = $this->actingAs($user)
            ->get(route('student.courses.learn', $course, ['lesson' => $lesson1->id]));

        $response->assertStatus(200);
        $response->assertSee($lesson1->title);

        // Check that lesson 1 is marked as active in the sidebar
        $response->assertSee('data-lesson-id="' . $lesson1->id . '"');

        // Test navigation to lesson 2
        $response = $this->actingAs($user)
            ->get(route('student.courses.learn', $course, ['lesson' => $lesson2->id]));

        $response->assertStatus(200);
        $response->assertSee($lesson2->title);

        // Check that lesson 2 is marked as active in the sidebar
        $response->assertSee('data-lesson-id="' . $lesson2->id . '"');
    }
}
