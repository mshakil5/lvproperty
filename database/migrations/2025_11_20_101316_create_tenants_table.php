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
            $table->foreignId('property_id')->nullable()->constrained()->onDelete('cascade');
            // Basic profile
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('address_first_line')->nullable();
            $table->string('city')->nullable();
            $table->string('postcode')->nullable();
            $table->string('emergency_contact');
            //Documents
            $table->enum('tenancy_agreement_status', ['yes', 'no'])->default('no');
            $table->date('tenancy_agreement_date')->nullable();
            $table->string('tenancy_agreement_document')->nullable();

            $table->enum('reference_check_status', ['yes', 'no'])->default('no');
            $table->date('reference_check_date')->nullable();
            $table->string('reference_check_document')->nullable();

            $table->enum('immigration_status', ['yes', 'no'])->default('no');
            $table->date('immigration_status_date')->nullable();
            $table->string('immigration_status_document')->nullable();
            
            $table->enum('right_to_rent_status', ['yes', 'no'])->default('no');
            $table->date('right_to_rent_date')->nullable();
            $table->string('right_to_rent_document')->nullable();

            $table->enum('previous_landlord_reference', ['yes', 'no'])->default('no');
            $table->date('previous_landlord_reference_date')->nullable();
            $table->string('previous_landlord_reference_document')->nullable();

            $table->enum('personal_reference', ['yes', 'no'])->default('no');
            $table->date('personal_reference_date')->nullable();
            $table->string('personal_reference_document')->nullable();

            // Bank Details
            $table->string('bank_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('sort_code')->nullable();

            // Additional Tenants (JSON)
            $table->json('additional_tenants')->nullable();
            
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
