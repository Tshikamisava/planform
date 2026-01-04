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
        Schema::create('impact_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('change_request_id')->constrained('dcrs')->onDelete('cascade');
            $table->foreignId('assessor_id')->constrained('users')->onDelete('restrict');
            
            // Impact Ratings
            $table->enum('impact_rating', ['Low', 'Medium', 'High', 'Critical'])->default('Low');
            $table->enum('business_impact', ['Low', 'Medium', 'High', 'Critical'])->default('Low');
            $table->enum('technical_impact', ['Low', 'Medium', 'High', 'Critical'])->default('Low');
            $table->enum('risk_level', ['Low', 'Medium', 'High', 'Critical'])->default('Low');
            
            // Assessment Details
            $table->text('impact_summary');
            $table->json('affected_systems')->nullable();
            $table->json('affected_users')->nullable();
            $table->string('downtime_estimate', 50)->nullable();
            $table->text('rollback_plan')->nullable();
            $table->text('testing_requirements')->nullable();
            
            // Recommendations
            $table->text('recommendations')->nullable();
            $table->text('conditions_for_approval')->nullable();
            $table->json('required_resources')->nullable();
            
            // Assessment Metadata
            $table->date('assessment_date');
            $table->date('next_review_date')->nullable();
            $table->enum('confidence_level', ['Low', 'Medium', 'High'])->default('Medium');
            
            $table->timestamps();

            // Indexes
            $table->index('change_request_id');
            $table->index('assessor_id');
            $table->index('impact_rating');
            $table->index('business_impact');
            $table->index('risk_level');
            $table->index('assessment_date');

            // Constraints
            $table->unique('change_request_id');
        });

        // Note: Check constraints with functions not supported in MySQL/MariaDB
        // These will be handled at application level
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('impact_assessments');
    }
};
