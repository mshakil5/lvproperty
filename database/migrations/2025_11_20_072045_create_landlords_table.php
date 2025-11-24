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
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();            // permanent address
            $table->text('current_address')->nullable();
            $table->text('previous_address')->nullable();

            // Bank details
            $table->string('bank_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('sort_code')->nullable();

            // Emergency contact
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->string('emergency_contact_relation')->nullable();

            // References
            $table->enum('reference_checked', ['yes', 'no', 'processing'])->default('no');
            $table->string('credit_score')->nullable();
            $table->text('previous_landlord_reference')->nullable();
            $table->text('personal_reference')->nullable();

            // Immigration / Right to rent
            $table->enum('right_to_rent_status', ['verified', 'not_verified', 'pending'])->default('pending');
            $table->date('right_to_rent_check_date')->nullable();

            // Landlord specific fields
            $table->enum('service_type', ['Full Management', 'Rent Collection', 'Tenant Finding'])->nullable();
            $table->decimal('management_fee', 5, 2)->nullable();
            $table->date('agreement_date')->nullable();
            $table->integer('agreement_duration')->nullable();
            $table->date('agreement_due_date')->nullable();

            // Status
            $table->boolean('status')->default(true);
            $table->timestamps();

            // Indexes
            $table->index('email');
            $table->index('status');
            $table->index('agreement_due_date');
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
