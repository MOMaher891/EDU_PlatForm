<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentSectionAccess extends Model
{
    use HasFactory;

    protected $table = 'student_section_access';

    protected $fillable = [
        'user_id',
        'course_id',
        'section_id',
        'payment_id',
        'price_paid',
        'access_granted_at',
        'access_expires_at',
        'is_active'
    ];

    protected $casts = [
        'access_granted_at' => 'datetime',
        'access_expires_at' => 'datetime',
        'is_active' => 'boolean',
        'price_paid' => 'decimal:2'
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function section()
    {
        return $this->belongsTo(CourseSection::class, 'section_id');
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    // Helper methods
    public function isExpired()
    {
        return $this->access_expires_at && $this->access_expires_at->isPast();
    }

    public function isActive()
    {
        return $this->is_active && !$this->isExpired();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                    ->where(function ($q) {
                        $q->whereNull('access_expires_at')
                          ->orWhere('access_expires_at', '>', now());
                    });
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForSection($query, $sectionId)
    {
        return $query->where('section_id', $sectionId);
    }
}
