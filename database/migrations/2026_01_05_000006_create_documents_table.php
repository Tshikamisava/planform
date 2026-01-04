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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->char('uuid', 36)->unique();
            $table->foreignId('change_request_id')->nullable()->constrained('dcrs')->onDelete('cascade');
            
            // Document Details
            $table->enum('document_type', ['Attachment', 'Supporting_Doc', 'Implementation_Doc', 'Closure_Doc', 'Audit_Doc'])->default('Attachment');
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->string('original_filename', 255);
            $table->string('stored_filename', 255);
            $table->string('file_path', 500);
            $table->bigInteger('file_size')->unsigned();
            $table->string('mime_type', 100);
            $table->string('file_hash', 64);
            
            // Version Control
            $table->tinyInteger('version')->unsigned()->default(1);
            $table->foreignId('parent_document_id')->nullable()->constrained('documents')->onDelete('set null');
            $table->boolean('is_latest')->default(true);
            
            // Access Control
            $table->boolean('is_public')->default(false);
            $table->enum('access_level', ['Public', 'Internal', 'Confidential', 'Restricted'])->default('Internal');
            
            // Metadata
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('restrict');
            $table->integer('download_count')->unsigned()->default(0);
            $table->timestamp('last_downloaded_at')->nullable();
            
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));

            // Indexes
            $table->index('uuid');
            $table->index('change_request_id');
            $table->index('document_type');
            $table->index('uploaded_by');
            $table->index('file_hash');
            $table->index('access_level');
            $table->index('created_at');
            $table->index('is_latest');

            // Composite indexes
            $table->index(['change_request_id', 'document_type']);
            $table->index(['change_request_id', 'is_latest']);

            // Constraints
            $table->unique('stored_filename');
        });

        // Note: Check constraints with functions not supported in MySQL/MariaDB
        // These will be handled at application level
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
