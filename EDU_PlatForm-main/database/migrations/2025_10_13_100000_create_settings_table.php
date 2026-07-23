<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up(): void
	{
		Schema::create('settings', function (Blueprint $table) {
			$table->id();
			$table->boolean('block_devtools')->default(false);
			$table->boolean('block_copy_text')->default(false);
			$table->timestamps();
		});

		// Seed a single row to hold global settings
		if (!DB::table('settings')->exists()) {
			DB::table('settings')->insert([
				'block_devtools' => false,
				'block_copy_text' => false,
				'created_at' => now(),
				'updated_at' => now(),
			]);
		}
	}

	public function down(): void
	{
		Schema::dropIfExists('settings');
	}
};


