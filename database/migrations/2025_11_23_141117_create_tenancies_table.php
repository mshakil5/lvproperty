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
        Schema::create('tenancies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('landlord_id')->constrained()->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->constrained('tenancies')->onDelete('cascade');
            
            // Financial Terms
            $table->decimal('amount', 10, 2);
            
            $table->integer('due_date')->default(20);
            
            // Tenancy Period
            $table->date('start_date');
            $table->date('end_date');
            
            // Status
            $table->boolean('status')->default(true);
            
            $table->text('note')->nullable();
            
            // Renewal Settings
            $table->boolean('auto_renew')->default(false);

            // Timestamps
            $table->timestamps();
            
            // Indexes
            $table->index(['status', 'end_date']);
            $table->index('tenant_id');
            $table->index('property_id');
            $table->index('parent_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenancies');
    }
};
