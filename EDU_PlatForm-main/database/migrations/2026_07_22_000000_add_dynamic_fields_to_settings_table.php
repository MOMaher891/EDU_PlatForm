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
        Schema::table('settings', function (Blueprint $table) {
            $table->string('platform_name')->nullable();
            $table->string('platform_logo')->nullable();
            $table->string('support_email')->nullable();
            $table->string('support_phone')->nullable();
            $table->text('platform_description')->nullable();
            $table->integer('max_courses_per_instructor')->default(10);
            $table->integer('max_lessons_per_course')->default(50);
            $table->integer('max_file_size')->default(10);
            $table->string('allowed_file_types')->default('pdf,doc,docx,ppt,pptx,mp4,avi,mov');
            $table->string('default_currency')->default('USD');
            $table->decimal('commission_rate', 5, 2)->default(10.00);
            $table->decimal('minimum_withdrawal', 10, 2)->default(50.00);
            $table->integer('payment_processing_days')->default(7);
            $table->string('mail_provider')->default('smtp');
            $table->string('from_email')->default('noreply@example.com');
            $table->string('from_name')->default('منصة التعلم');
            $table->boolean('email_notifications')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'platform_name',
                'platform_logo',
                'support_email',
                'support_phone',
                'platform_description',
                'max_courses_per_instructor',
                'max_lessons_per_course',
                'max_file_size',
                'allowed_file_types',
                'default_currency',
                'commission_rate',
                'minimum_withdrawal',
                'payment_processing_days',
                'mail_provider',
                'from_email',
                'from_name',
                'email_notifications',
            ]);
        });
    }
};
