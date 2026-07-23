<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'title',
        'description',
        'order_index',
        'is_active',
        'price',
        'discount_price',
        'is_purchasable_separately'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_purchasable_separately' => 'boolean',
        'price' => 'decimal:2',
        'discount_price' => 'decimal:2'
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function lessons()
    {
        return $this->hasMany(Lesson::class, 'section_id')->orderBy('order_index');
    }

    public function studentAccess()
    {
        return $this->hasMany(StudentSectionAccess::class, 'section_id');
    }

    // Helper methods
    public function getEffectivePrice()
    {
        return $this->discount_price ?? $this->price;
    }

    public function getDiscountPercentage()
    {
        if (!$this->discount_price || $this->price <= 0) {
            return 0;
        }

        return round((($this->price - $this->discount_price) / $this->price) * 100);
    }

    public function getTotalLessons()
    {
        return $this->lessons->count();
    }

    public function getTotalDuration()
    {
        return $this->lessons->sum('duration_minutes');
    }

    public function hasStudentAccess($userId)
    {
        return $this->studentAccess()
                    ->active()
                    ->forUser($userId)
                    ->exists();
    }

    public function isPurchasable()
    {
        return $this->is_purchasable_separately && $this->price > 0;
    }
}
