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
        Schema::table('course_sections', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->nullable()->after('is_active');
            $table->decimal('discount_price', 10, 2)->nullable()->after('price');
            $table->boolean('is_purchasable_separately')->default(false)->after('discount_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_sections', function (Blueprint $table) {
            $table->dropColumn(['price', 'discount_price', 'is_purchasable_separately']);
        });
    }
};
