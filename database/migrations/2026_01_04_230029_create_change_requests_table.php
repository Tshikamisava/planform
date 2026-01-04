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
        Schema::create('change_requests', function (Blueprint $table) {
            $table->id();
            $table->char('uuid', 36)->unique();
            $table->string('dcr_id')->unique();
            $table->string('title', 255);
            $table->text('description');
            $table->text('reason_for_change');
            $table->enum('request_type', ['Standard', 'Emergency', 'Routine', 'Corrective'])->default('Standard');
            $table->enum('priority', ['Low', 'Medium', 'High', 'Critical'])->default('Medium');
            $table->date('due_date');
            $table->enum('status', ['Draft', 'Pending', 'In_Review', 'Approved', 'Rejected', 'In_Progress', 'Completed', 'Closed', 'Cancelled'])->default('Draft');
            $table->enum('impact', ['Low', 'Medium', 'High'])->nullable();
            $table->text('impact_summary')->nullable();
            $table->text('recommendations')->nullable();
            $table->unsignedBigInteger('author_id');
            $table->unsignedBigInteger('recipient_id')->nullable();
            $table->unsignedBigInteger('decision_maker_id')->nullable();
            $table->json('attachments')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Foreign key constraints
            $table->foreign('author_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('recipient_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('decision_maker_id')->references('id')->on('users')->onDelete('set null');
            
            // Indexes
            $table->index('uuid');
            $table->index('dcr_id');
            $table->index('author_id');
            $table->index('recipient_id');
            $table->index('decision_maker_id');
            $table->index('status');
            $table->index('priority');
            $table->index('due_date');
            $table->index('created_at');
            
            // Composite indexes
            $table->index(['status', 'priority']);
            $table->index(['author_id', 'status']);
            $table->index(['recipient_id', 'status']);
            $table->index(['decision_maker_id', 'status']);
            $table->index(['due_date', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('change_requests');
    }
};
