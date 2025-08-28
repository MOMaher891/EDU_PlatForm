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
        Schema::table('payments', function (Blueprint $table) {
            // Check if payment_id column exists, if not add it
            if (!Schema::hasColumn('payments', 'payment_id')) {
                $table->string('payment_id')->after('payment_method');
            }

            // Check if transaction_data column exists, if not add it
            if (!Schema::hasColumn('payments', 'transaction_data')) {
                $table->json('transaction_data')->nullable()->after('payment_id');
            }

            // Check if currency column exists, if not add it
            if (!Schema::hasColumn('payments', 'currency')) {
                $table->string('currency', 3)->default('USD')->after('amount');
            }

            // Remove old columns if they exist
            if (Schema::hasColumn('payments', 'transaction_id')) {
                $table->dropColumn('transaction_id');
            }

            if (Schema::hasColumn('payments', 'payment_data')) {
                $table->dropColumn('payment_data');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Revert changes if needed
            if (Schema::hasColumn('payments', 'payment_id')) {
                $table->dropColumn('payment_id');
            }

            if (Schema::hasColumn('payments', 'transaction_data')) {
                $table->dropColumn('transaction_data');
            }

            if (Schema::hasColumn('payments', 'currency')) {
                $table->dropColumn('currency');
            }

            // Add back old columns
            $table->string('transaction_id')->after('payment_method');
            $table->json('payment_data')->nullable()->after('status');
        });
    }
};
