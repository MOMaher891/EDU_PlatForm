<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->nullable()->after('id')->constrained('roles');
        });

        // Seed basic roles and backfill role_id from existing enum column
        $roles = [
            ['name' => 'Administrator', 'slug' => 'admin'],
            ['name' => 'Instructor', 'slug' => 'instructor'],
            ['name' => 'Student', 'slug' => 'student'],
        ];

        foreach ($roles as $role) {
            $exists = DB::table('roles')->where('slug', $role['slug'])->exists();
            if (!$exists) {
                DB::table('roles')->insert($role + ['created_at' => now(), 'updated_at' => now()]);
            }
        }

        // Map users.role string -> role_id
        $roleMap = DB::table('roles')->pluck('id', 'slug');
        foreach (['admin', 'instructor', 'student'] as $slug) {
            if (isset($roleMap[$slug])) {
                DB::table('users')->where('role', $slug)->update(['role_id' => $roleMap[$slug]]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('role_id');
        });
    }
};


