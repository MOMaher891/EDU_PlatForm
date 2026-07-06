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
        'price',
        'is_free',
        'is_active'
    ];

    protected $casts = [
        'is_free' => 'boolean',
        'is_active' => 'boolean',
        'video_duration' => 'integer',
        'file_size' => 'integer',
        'price' => 'decimal:2'
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
            return asset('storage/' . ltrim($this->file_path, '/'));
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
     * Get the embedded video URL for YouTube, Vimeo, or Telegram
     */
    public function getVideoEmbedUrlAttribute()
    {
        $url = $this->video_url;
        if (!$url) {
            return null;
        }

        // YouTube parsing
        if (str_contains($url, 'youtube.com') || str_contains($url, 'youtu.be')) {
            $videoId = null;
            if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $match)) {
                $videoId = $match[1];
            }
            return $videoId ? 'https://www.youtube.com/embed/' . $videoId : null;
        }

        // Vimeo parsing
        if (str_contains($url, 'vimeo.com')) {
            $vimeoId = null;
            if (preg_match('%vimeo\.com/(?:channels/(?:\w+/)?|groups/(?:[^\/]*)/videos/|album/(?:\d+)/video/|video/|)(\d+)(?:$|[?&])%i', $url, $match)) {
                $vimeoId = $match[1];
            }
            return $vimeoId ? 'https://player.vimeo.com/video/' . $vimeoId : null;
        }

        // Telegram parsing
        if (str_contains($url, 't.me/') || str_contains($url, 'telegram.me/')) {
            if (preg_match('%(?:t\.me|telegram\.me)/(?:c/)?([^/]+)/(\d+)(?:$|[/?&])%i', $url, $matches)) {
                $isPrivate = str_contains($url, '/c/');
                $channelId = $matches[1];
                $messageId = $matches[2];
                if ($isPrivate) {
                    return 'https://t.me/c/' . $channelId . '/' . $messageId . '?embed=1';
                }
                return 'https://t.me/' . $channelId . '/' . $messageId . '?embed=1';
            }
        }

        // Google Drive parsing
        if (str_contains($url, 'drive.google.com') || str_contains($url, 'docs.google.com')) {
            $driveId = null;
            if (preg_match('%(?:drive|docs)\.google\.com/(?:file/d/|open\?id=)([a-zA-Z0-9_-]+)%i', $url, $match)) {
                $driveId = $match[1];
            }
            return $driveId ? 'https://drive.google.com/file/d/' . $driveId . '/preview' : null;
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
