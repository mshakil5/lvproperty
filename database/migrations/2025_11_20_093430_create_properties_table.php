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
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
             // Relationship
            $table->foreignId('landlord_id')->constrained()->onDelete('cascade')->index();

            // Basic property info
            $table->string('property_reference')->nullable()->index();

            $table->enum('property_type', ['House', 'Flat', 'Apartment', 'Commercial'])->default('House');

            $table->enum('status', ['Vacant', 'Occupied', 'Maintenance'])->default('Vacant')->index();
            $table->date('status_until_date')->nullable();

            $table->string('address_first_line')->nullable();
            $table->string('city')->nullable();
            $table->string('postcode')->nullable();

            $table->string('emergency_contact')->nullable();

            // Representative Details
            $table->string('representative_name')->nullable();
            $table->string('representative_contact')->nullable();
            $table->string('representative_authorisation')->nullable();
            $table->string('representative_authorisation_file')->nullable();

            // Service Agreement
            $table->string('service_type')->nullable();
            $table->decimal('management_fee', 5, 2)->nullable();
            $table->date('agreement_date')->nullable();
            $table->integer('agreement_duration')->nullable();

            // Service Technician Details as JSON
            $table->json('technicians')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
