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

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}


