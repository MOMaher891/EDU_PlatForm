<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'avatar',
        'bio',
        'phone',
        'date_of_birth',
        'email_verified_at',
        'deleted_at',
        'is_active'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'date_of_birth' => 'date'
    ];

    // Role checking methods
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isInstructor()
    {
        return $this->role === 'instructor';
    }

    public function isStudent()
    {
        return $this->role === 'student';
    }

    // Relationships
    public function enrollments()
    {
        return $this->hasMany(CourseEnrollment::class,'user_id');
    }

    public function instructedCourses()
    {
        return $this->hasMany(Course::class, 'instructor_id');
    }

    public function activities()
    {
        return $this->hasMany(Activity::class);
    }

    public function lessonProgress()
    {
        return $this->hasMany(LessonProgress::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function favorites()
    {
        return $this->belongsToMany(Course::class, 'course_favorites');
    }

    public function sectionAccess()
    {
        return $this->hasMany(StudentSectionAccess::class);
    }

    public function hasSectionAccess($sectionId)
    {
        return $this->sectionAccess()
                    ->active()
                    ->forSection($sectionId)
                    ->exists();
    }

    public function getAccessibleSections()
    {
        return $this->sectionAccess()
                    ->active()
                    ->with(['section', 'course'])
                    ->get()
                    ->pluck('section');
    }
}
