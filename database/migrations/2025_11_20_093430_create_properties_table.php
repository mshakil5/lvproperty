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
            $table->foreignId('landlord_id')->constrained()->onDelete('cascade');

            // Basic property info
            $table->string('property_name')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('postcode')->nullable();
            $table->enum('property_type', ['House', 'Flat', 'Apartment', 'Commercial'])->default('House');
            $table->decimal('rent_amount', 10, 2)->nullable();

            // Additional new requested details
            $table->string('representative_name')->nullable();
            $table->string('representative_authorisation')->nullable();
            $table->string('representative_emergency_contact')->nullable();

            // Service technician details
            $table->string('technician_name')->nullable();
            $table->string('technician_phone')->nullable();
            $table->string('technician_email')->nullable();

            // Status
            $table->enum('status', ['Vacant', 'Occupied', 'Maintenance'])->default('Vacant');
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
