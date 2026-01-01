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
        // Step 1: Add received_amount
        Schema::table('transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('transactions', 'received_amount')) {
                $table->decimal('received_amount', 10, 2)->default(0)->after('amount');
            }

            if (Schema::hasColumn('transactions', 'received_id')) {
                $table->dropForeign(['received_id']);
                $table->dropColumn('received_id');
            }
        });

        // Step 2: Add received_ids AFTER received_amount
        Schema::table('transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('transactions', 'received_ids')) {
                $table->json('received_ids')->nullable()->after('received_amount');
            }
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Drop columns only if they exist
            if (Schema::hasColumn('transactions', 'received_amount')) {
                $table->dropColumn('received_amount');
            }

            if (Schema::hasColumn('transactions', 'received_ids')) {
                $table->dropColumn('received_ids');
            }

            // Recreate received_id column if it does not exist
            if (!Schema::hasColumn('transactions', 'received_id')) {
                $table->unsignedBigInteger('received_id')->nullable();
                $table->foreign('received_id')
                    ->references('id')
                    ->on('transactions')
                    ->nullOnDelete();
            }
        });
    }

};
