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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->char('uuid', 36)->unique();
            
            // Notification Details
            $table->string('type', 50);
            $table->enum('category', ['Info', 'Warning', 'Error', 'Success', 'System'])->default('Info');
            $table->string('title', 255);
            $table->text('message');
            
            // Recipients
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('email', 255)->nullable();
            $table->foreignId('role_id')->nullable()->constrained()->onDelete('cascade');
            
            // Channels
            $table->boolean('via_email')->default(false);
            $table->boolean('via_in_app')->default(true);
            $table->boolean('via_sms')->default(false);
            
            // Status
            $table->enum('status', ['Pending', 'Sent', 'Delivered', 'Read', 'Failed'])->default('Pending');
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            
            // Context
            $table->string('related_resource_type', 50)->nullable();
            $table->bigInteger('related_resource_id')->unsigned()->nullable();
            $table->text('action_url')->nullable();
            
            // Scheduling
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            
            // Metadata
            $table->enum('priority', ['Low', 'Normal', 'High', 'Urgent'])->default('Normal');
            $table->tinyInteger('retry_count')->unsigned()->default(0);
            $table->tinyInteger('max_retries')->unsigned()->default(3);
            
            $table->timestamps();

            // Indexes
            $table->index('uuid');
            $table->index('type');
            $table->index('category');
            $table->index('user_id');
            $table->index('email');
            $table->index('role_id');
            $table->index('status');
            $table->index('priority');
            $table->index('scheduled_at');
            $table->index('created_at');

            // Composite indexes
            $table->index(['user_id', 'status']);
            $table->index(['status', 'priority']);
            $table->index(['scheduled_at', 'status']);
        });

        // Note: Check constraints with functions not supported in MySQL/MariaDB
        // These will be handled at application level
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
