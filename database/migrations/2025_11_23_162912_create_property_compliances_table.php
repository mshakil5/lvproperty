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
        Schema::create('property_compliances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->foreignId('compliance_type_id')->constrained()->onDelete('cascade');
            
            // Certificate Details
            $table->string('certificate_number')->nullable();
            $table->date('issue_date');
            $table->date('expiry_date');
            $table->date('renewal_date')->nullable();
            
            // Status & Tracking
            $table->enum('status', ['Active', 'Expired', 'Renewed', 'Pending'])->default('Active');
            $table->text('notes')->nullable();
            
            // File Attachment
            $table->string('document_path')->nullable(); // PDF file path
            
            // Payment Info
            $table->decimal('cost', 10, 2)->nullable();
            $table->enum('paid_by', ['Landlord', 'Tenant'])->default('Landlord');
            
            $table->timestamps();
            
            // Indexes for performance
            $table->index('expiry_date');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_compliances');
    }
};
