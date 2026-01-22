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
        Schema::table('dcrs', function (Blueprint $table) {
            // Verification & Validation
            $table->boolean('is_verified')->default(false)->after('status');
            $table->foreignId('verified_by')->nullable()->constrained('users')->after('is_verified');
            $table->timestamp('verified_at')->nullable()->after('verified_by');
            $table->text('verification_notes')->nullable()->after('verified_at');
            
            $table->boolean('is_validated')->default(false)->after('verification_notes');
            $table->foreignId('validated_by')->nullable()->constrained('users')->after('is_validated');
            $table->timestamp('validated_at')->nullable()->after('validated_by');
            $table->text('validation_notes')->nullable()->after('validated_at');
            
            // Closure Process
            $table->enum('closure_status', ['Open', 'Pending_Closure', 'Closed'])->default('Open')->after('validation_notes');
            $table->foreignId('closed_by')->nullable()->constrained('users')->after('closure_status');
            $table->text('closure_notes')->nullable()->after('closed_at');
            $table->json('closure_checklist')->nullable()->after('closure_notes');
            
            // Record Locking
            $table->foreignId('locked_by')->nullable()->constrained('users')->after('is_locked');
            $table->timestamp('locked_at')->nullable()->after('locked_by');
            $table->string('lock_reason')->nullable()->after('locked_at');
            
            // Document Archiving
            $table->boolean('is_archived')->default(false)->after('lock_reason');
            $table->foreignId('archived_by')->nullable()->constrained('users')->after('is_archived');
            $table->timestamp('archived_at')->nullable()->after('archived_by');
            $table->string('archive_location')->nullable()->after('archived_at');
            
            // Compliance Tracking
            $table->json('compliance_metadata')->nullable()->after('archive_location');
            $table->timestamp('last_modified_at')->nullable()->after('compliance_metadata');
            $table->foreignId('last_modified_by')->nullable()->constrained('users')->after('last_modified_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dcrs', function (Blueprint $table) {
            $table->dropForeign(['verified_by']);
            $table->dropForeign(['validated_by']);
            $table->dropForeign(['closed_by']);
            $table->dropForeign(['locked_by']);
            $table->dropForeign(['archived_by']);
            $table->dropForeign(['last_modified_by']);
            
            $table->dropColumn([
                'is_verified', 'verified_by', 'verified_at', 'verification_notes',
                'is_validated', 'validated_by', 'validated_at', 'validation_notes',
                'closure_status', 'closed_by', 'closure_notes', 'closure_checklist',
                'locked_by', 'locked_at', 'lock_reason',
                'is_archived', 'archived_by', 'archived_at', 'archive_location',
                'compliance_metadata', 'last_modified_at', 'last_modified_by'
            ]);
        });
    }
};
