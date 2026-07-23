<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    // database/migrations/xxxx_xx_xx_create_sessions_table.php

    public function up()
    {
        Schema::create('sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('instructor_id')->constrained('users')->onDelete('cascade');

            $table->string('title');
            $table->text('description')->nullable();

            $table->enum('type', ['live', 'recorded'])->default('live'); // نوع الجلسة
            $table->string('platform')->nullable(); // Zoom - Google Meet - ...
            $table->string('meeting_url')->nullable(); // رابط الجلسة

            $table->dateTime('start_time');
            $table->integer('duration_minutes')->nullable(); // المدة

            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('session');
    }
};
