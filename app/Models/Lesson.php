<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Lesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'section_id',
        'title',
        'content',
        'video_url',
        'video_duration',
        'file_path',
        'file_type',
        'file_name',
        'file_size',
        'mime_type',
        'order_index',
        'is_free',
        'is_active'
    ];

    protected $casts = [
        'is_free' => 'boolean',
        'is_active' => 'boolean',
        'video_duration' => 'integer',
        'file_size' => 'integer'
    ];

    public function section()
    {
        return $this->belongsTo(CourseSection::class, 'section_id');
    }

    public function course()
    {
        return $this->hasOneThrough(Course::class, CourseSection::class, 'id', 'id', 'section_id', 'course_id');
    }

    public function progress()
    {
        return $this->hasMany(LessonProgress::class);
    }

    public function activities()
    {
        return $this->hasMany(Activity::class);
    }

    /**
     * Get the file URL for the lesson
     */
    public function getFileUrlAttribute()
    {
        if ($this->file_path && Storage::disk('public')->exists($this->file_path)) {
            return Storage::disk('public')->url($this->file_path);
        }
        return null;
    }

    /**
     * Get the file size in human readable format
     */
    public function getFileSizeHumanAttribute()
    {
        if (!$this->file_size) {
            return null;
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $size = $this->file_size;
        $unit = 0;

        while ($size >= 1024 && $unit < count($units) - 1) {
            $size /= 1024;
            $unit++;
        }

        return round($size, 2) . ' ' . $units[$unit];
    }

    /**
     * Check if the lesson has a file
     */
    public function hasFile()
    {
        return !empty($this->file_path) && Storage::disk('public')->exists($this->file_path);
    }

    /**
     * Check if the lesson has a video
     */
    public function hasVideo()
    {
        // Check if there's an external video URL
        if (!empty($this->video_url)) {
            return true;
        }

        // Check if there's an uploaded video file
        if ($this->file_type === 'video' && $this->hasFile()) {
            return true;
        }

        return false;
    }

    /**
     * Get the video URL (either external URL or uploaded file)
     */
    public function getVideoUrlAttribute()
    {
        // If there's an external video URL, return it
        if ($this->attributes['video_url'] ?? null) {
            return $this->attributes['video_url'];
        }

        // If there's an uploaded video file, return its URL
        if ($this->file_type === 'video' && $this->hasFile()) {
            return $this->file_url;
        }

        return null;
    }

    /**
     * Check if the file is an image
     */
    public function isImage()
    {
        if (!$this->mime_type) {
            return false;
        }

        return str_starts_with($this->mime_type, 'image/');
    }

    /**
     * Check if the file is a video
     */
    public function isVideo()
    {
        // Check by file_type first (more reliable)
        if ($this->file_type === 'video') {
            return true;
        }

        // Fallback to mime_type check
        if ($this->mime_type) {
            return str_starts_with($this->mime_type, 'video/');
        }

        return false;
    }

    /**
     * Check if the file is a PDF
     */
    public function isPdf()
    {
        return $this->mime_type === 'application/pdf';
    }

    /**
     * Check if the file is a document
     */
    public function isDocument()
    {
        $documentTypes = [
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'text/plain'
        ];

        return in_array($this->mime_type, $documentTypes);
    }

    /**
     * Get the file icon based on type
     */
    public function getFileIconAttribute()
    {
        if ($this->isImage()) {
            return 'fas fa-image';
        } elseif ($this->isVideo()) {
            return 'fas fa-video';
        } elseif ($this->isPdf()) {
            return 'fas fa-file-pdf';
        } elseif ($this->isDocument()) {
            return 'fas fa-file-word';
        } else {
            return 'fas fa-file';
        }
    }

    /**
     * Delete the associated file when the lesson is deleted
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($lesson) {
            if ($lesson->file_path && Storage::disk('public')->exists($lesson->file_path)) {
                Storage::disk('public')->delete($lesson->file_path);
            }
        });
    }
}
