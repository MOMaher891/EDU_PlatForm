<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE lessons MODIFY COLUMN file_type ENUM('video', 'pdf', 'document', 'quiz', 'image') DEFAULT 'video'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE lessons MODIFY COLUMN file_type ENUM('video', 'pdf', 'document', 'quiz') DEFAULT 'video'");
    }
};
