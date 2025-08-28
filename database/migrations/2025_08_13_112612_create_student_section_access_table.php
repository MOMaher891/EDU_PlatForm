<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('student_section_access', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('section_id')->constrained('course_sections')->onDelete('cascade');
            $table->foreignId('payment_id')->nullable()->constrained()->onDelete('set null');
            $table->decimal('price_paid', 10, 2);
            $table->timestamp('access_granted_at');
            $table->timestamp('access_expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Ensure a user can only have one active access record per section
            $table->unique(['user_id', 'section_id'], 'unique_user_section_access');

            // Indexes for better performance
            $table->index(['user_id', 'course_id']);
            $table->index(['section_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_section_access');
    }
};
