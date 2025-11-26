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
        Schema::create('landlords', function (Blueprint $table) {
            $table->id();
             // Basic profile
            $table->string('name')->nullable();
            $table->string('company_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('postcode')->nullable();
            $table->text('correspondence_address')->nullable();

            // Compliance (File uploads)
            $table->string('proof_of_id')->nullable();
            $table->string('authorisation_letter')->nullable();
            $table->string('landlord_agent_agreement')->nullable();

            // Bank Details
            $table->string('bank_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('sort_code')->nullable();

            // Status
            $table->boolean('status')->default(true);

            $table->timestamps();

            // Indexes
            $table->index('email');
            $table->index('phone');
            $table->index('postcode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('landlords');
    }
};
