<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lesson_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->text('lessons_ids'); // e.g., "1,2,3"
            $table->decimal('total_cost', 10, 2);
            $table->string('attachment_path');
            $table->unsignedTinyInteger('status')->default(0); // 0: pending, 1: accept, 2: reject
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lesson_payments');
    }
};


