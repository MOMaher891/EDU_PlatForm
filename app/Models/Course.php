<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

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

    protected static function boot()
    {
        parent::boot();

        static::creating(function (Course $course) {
            if (empty($course->slug) && !empty($course->title)) {
                $base = Str::slug($course->title);
                $slug = $base;
                $counter = 1;
                while (static::where('slug', $slug)->exists()) {
                    $slug = $base . '-' . $counter++;
                }
                $course->slug = $slug;
            }
        });

        static::updating(function (Course $course) {
            if ($course->isDirty('title') && empty($course->slug)) {
                $course->slug = Str::slug($course->title);
            }
        });

        static::saved(function () {
            \Illuminate\Support\Facades\Cache::forget('public_all_categories');
        });

        static::deleted(function () {
            \Illuminate\Support\Facades\Cache::forget('public_all_categories');
        });
    }

    /**
     * Get the route key for the model to enable SEO-friendly slugs in URLs.
     *
     * @return string
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Retrieve the model for a bound value.
     * Allows resolving by numeric ID or slug.
     *
     * @param  mixed  $value
     * @param  string|null  $field
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function resolveRouteBinding($value, $field = null)
    {
        $field = $field ?? $this->getRouteKeyName();

        if (is_numeric($value)) {
            $course = static::where('id', $value)->first();
            if ($course) {
                return $course;
            }
        }

        return static::where($field, $value)->firstOrFail();
    }

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
        if ($this->relationLoaded('sections')) {
            return $this->sections->sum(function ($section) {
                return $section->relationLoaded('lessons') ? $section->lessons->count() : $section->lessons()->count();
            });
        }

        return \App\Models\Lesson::whereIn('section_id', $this->sections()->pluck('id'))->count();
    }

    public function getFormattedDurationHours()
    {
        if ($this->duration_hours && $this->duration_hours > 0) {
            return $this->duration_hours;
        }

        $totalSeconds = \App\Models\Lesson::whereIn('section_id', $this->sections()->pluck('id'))->sum('video_duration');
        if ($totalSeconds > 0) {
            $hours = round($totalSeconds / 3600, 1);
            return $hours > 0 ? $hours : 1;
        }

        return 0;
    }

    public function getThumbnailUrlAttribute()
    {
        if ($this->thumbnail) {
            if (str_starts_with($this->thumbnail, 'http://') || str_starts_with($this->thumbnail, 'https://')) {
                return $this->thumbnail;
            }
            $cleanPath = ltrim(str_replace(['/storage/', '/media/'], '', $this->thumbnail), '/');

            // Automatic WebP fallback detection for optimized image delivery
            $webpPath = preg_replace('/\.(jpg|jpeg|png)$/i', '.webp', $cleanPath);
            if ($webpPath !== $cleanPath && file_exists(public_path('media/' . $webpPath))) {
                return url('media/' . $webpPath);
            }

            return url('media/' . $cleanPath);
        }
        return asset('images/default.png');
    }

    public function getPreviewVideoUrlAttribute()
    {
        if ($this->preview_video) {
            if (str_starts_with($this->preview_video, 'http://') || str_starts_with($this->preview_video, 'https://')) {
                return $this->preview_video;
            }
            $cleanPath = ltrim(str_replace(['/storage/', '/media/'], '', $this->preview_video), '/');
            return url('media/' . $cleanPath);
        }
        return null;
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
