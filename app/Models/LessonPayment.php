<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LessonPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'course_id',
        'lessons_ids',
        'total_cost',
        'attachment_path',
        'status',
    ];

    protected $casts = [
        'total_cost' => 'decimal:2',
        'status' => 'integer',
    ];

    public function getAttachmentUrlAttribute()
    {
        if ($this->attachment_path) {
            if (str_starts_with($this->attachment_path, 'http://') || str_starts_with($this->attachment_path, 'https://')) {
                return $this->attachment_path;
            }
            $cleanPath = ltrim(str_replace(['/storage/', '/media/'], '', $this->attachment_path), '/');
            return url('media/' . $cleanPath);
        }
        return null;
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    protected $selectedLessonsCache = null;

    public function getSelectedLessonsAttribute()
    {
        if ($this->selectedLessonsCache !== null) {
            return $this->selectedLessonsCache;
        }

        $ids = array_filter(array_map('trim', explode(',', (string) $this->lessons_ids)));
        if (empty($ids)) {
            return $this->selectedLessonsCache = collect();
        }

        return $this->selectedLessonsCache = Lesson::with('section')->whereIn('id', $ids)->get();
    }

    public function getSectionsNamesAttribute()
    {
        $lessons = $this->selected_lessons;
        if ($lessons->isEmpty()) {
            return '-';
        }

        $sections = $lessons->pluck('section.title')->filter()->unique();
        return $sections->isNotEmpty() ? $sections->implode(', ') : '-';
    }

    public function getLessonsNamesAttribute()
    {
        $lessons = $this->selected_lessons;
        if ($lessons->isEmpty()) {
            return '-';
        }

        return $lessons->pluck('title')->implode(', ');
    }
}


