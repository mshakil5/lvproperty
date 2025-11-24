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
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone');
            $table->text('address')->nullable(); // Permanent address
            $table->text('current_address');
            $table->text('previous_address')->nullable();
            $table->string('document')->nullable();
            
            // Bank details
            $table->string('bank_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('sort_code')->nullable();
            
            // Emergency contact
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->string('emergency_contact_relation')->nullable();
            
            // References
            $table->enum('reference_checked', ['Yes', 'No', 'Processing'])->default('No');
            $table->text('previous_landlord_reference')->nullable();
            $table->text('personal_reference')->nullable();
            $table->string('credit_score')->nullable();
            
            // Immigration / Right to rent
            $table->enum('immigration_status', ['Checked', 'Pending', 'Not Checked'])->default('Not Checked');
            $table->enum('right_to_rent_status', ['Verified', 'Not Verified', 'Pending'])->default('Pending');
            $table->date('right_to_rent_check_date')->nullable();
            
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
