<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'short_description',
        'description',
        'thumbnail',
        'preview_video',
        'price',
        'discount_price',
        'level',
        'duration_hours',
        'category_id',
        'instructor_id',
        'is_published',
        'is_featured',
        'requirements',
        'what_you_learn',
        'meta_title',
        'meta_description'
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'is_featured' => 'boolean',
        'price' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'requirements' => 'array',
        'what_you_learn' => 'array'
    ];

    // Relationships
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function sections()
    {
        return $this->hasMany(CourseSection::class)->orderBy('order_index');
    }

    public function enrollments()
    {
        return $this->hasMany(CourseEnrollment::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function activities()
    {
        return $this->hasMany(Activity::class);
    }

    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'course_favorites');
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
        return $this->sections->sum(function ($section) {
            return $section->lessons->count();
        });
    }

    public function getAverageRating()
    {
        return $this->reviews()->avg('rating') ?? 0;
    }

    public function getTotalReviews()
    {
        return $this->reviews()->count();
    }
}
