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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->char('uuid', 36)->unique();
            
            // Event Details
            $table->string('event_type', 50);
            $table->enum('event_category', ['Authentication', 'Authorization', 'CRUD', 'Workflow', 'System', 'Security']);
            $table->string('action', 100);
            
            // User Context
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('user_email', 255)->nullable();
            $table->string('session_id', 255)->nullable();
            
            // Resource Context
            $table->string('resource_type', 50)->nullable();
            $table->bigInteger('resource_id')->unsigned()->nullable();
            $table->char('resource_uuid', 36)->nullable();
            
            // Change Details
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->json('changed_fields')->nullable();
            
            // Request Context
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('request_method', 10)->nullable();
            $table->text('request_url')->nullable();
            
            // System Context
            $table->string('hostname', 255)->nullable();
            $table->string('application_version', 50)->nullable();
            
            // Result
            $table->boolean('success')->default(true);
            $table->text('error_message')->nullable();
            
            // Timestamps
            $table->timestamp('event_timestamp', 3)->default(DB::raw('CURRENT_TIMESTAMP(3)'));
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));

            // Indexes
            $table->index('uuid');
            $table->index('event_type');
            $table->index('event_category');
            $table->index('action');
            $table->index('user_id');
            $table->index('user_email');
            $table->index('resource_type');
            $table->index('resource_id');
            $table->index('resource_uuid');
            $table->index('ip_address');
            $table->index('success');
            $table->index('event_timestamp');

            // Composite indexes
            $table->index(['user_id', 'event_timestamp']);
            $table->index(['resource_type', 'resource_id', 'event_timestamp']);
            $table->index(['event_category', 'event_timestamp']);
        });

        // Note: Check constraints with functions not supported in MySQL/MariaDB
        // These will be handled at application level
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
