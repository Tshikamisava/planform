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
        Schema::create('approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('change_request_id')->constrained('dcrs')->onDelete('cascade');
            $table->foreignId('approver_id')->constrained('users')->onDelete('restrict');
            
            // Approval Details
            $table->enum('decision', ['Approved', 'Rejected', 'Approved_with_Conditions']);
            $table->enum('approval_level', ['Initial', 'Final', 'Escalated'])->default('Initial');
            
            // Decision Context
            $table->text('decision_reason');
            $table->json('conditions')->nullable();
            $table->text('recommendations')->nullable();
            
            // Approval Workflow
            $table->tinyInteger('sequence_order')->unsigned()->default(1);
            $table->boolean('is_final')->default(false);
            $table->boolean('requires_next_approval')->default(false);
            
            // Dates
            $table->timestamp('decided_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->date('effective_date')->nullable();
            $table->date('expiry_date')->nullable();
            
            // Metadata
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('digital_signature', 255)->nullable();
            
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));

            // Indexes
            $table->index('change_request_id');
            $table->index('approver_id');
            $table->index('decision');
            $table->index('approval_level');
            $table->index('decided_at');
            $table->index('sequence_order');

            // Composite indexes
            $table->index(['change_request_id', 'sequence_order']);
            $table->index(['approver_id', 'decision']);

            // Constraints
            $table->unique(['change_request_id', 'approver_id', 'sequence_order']);
        });

        // Note: Check constraints with functions not supported in MySQL/MariaDB
        // These will be handled at application level
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approvals');
    }
};
