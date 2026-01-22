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
        // Add indexes to dcrs table for better query performance
        Schema::table('dcrs', function (Blueprint $table) {
            $indexes = Schema::getIndexListing('dcrs');
            
            if (!in_array('idx_dcrs_author', $indexes)) $table->index('author_id', 'idx_dcrs_author');
            if (!in_array('idx_dcrs_recipient', $indexes)) $table->index('recipient_id', 'idx_dcrs_recipient');
            if (!in_array('idx_dcrs_decision_maker', $indexes)) $table->index('decision_maker_id', 'idx_dcrs_decision_maker');
            if (!in_array('idx_dcrs_status', $indexes)) $table->index('status', 'idx_dcrs_status');
            if (!in_array('idx_dcrs_created_at', $indexes)) $table->index('created_at', 'idx_dcrs_created_at');
            if (!in_array('idx_dcrs_due_date', $indexes)) $table->index('due_date', 'idx_dcrs_due_date');
            
            // Only add indexes for columns that exist
            if (Schema::hasColumn('dcrs', 'impact') && !in_array('idx_dcrs_impact', $indexes)) {
                $table->index('impact', 'idx_dcrs_impact');
            }
            if (Schema::hasColumn('dcrs', 'priority') && !in_array('idx_dcrs_priority', $indexes)) {
                $table->index('priority', 'idx_dcrs_priority');
            }
            if (Schema::hasColumn('dcrs', 'auto_escalated') && !in_array('idx_dcrs_auto_escalated', $indexes)) {
                $table->index('auto_escalated', 'idx_dcrs_auto_escalated');
            }
            
            // Composite indexes for common query patterns
            if (!in_array('idx_dcrs_status_created', $indexes)) $table->index(['status', 'created_at'], 'idx_dcrs_status_created');
            if (!in_array('idx_dcrs_status_due', $indexes)) $table->index(['status', 'due_date'], 'idx_dcrs_status_due');
            if (!in_array('idx_dcrs_recipient_status', $indexes)) $table->index(['recipient_id', 'status'], 'idx_dcrs_recipient_status');
            if (!in_array('idx_dcrs_dm_status', $indexes)) $table->index(['decision_maker_id', 'status'], 'idx_dcrs_dm_status');
            if (!in_array('idx_dcrs_author_created', $indexes)) $table->index(['author_id', 'created_at'], 'idx_dcrs_author_created');
            
            if (Schema::hasColumn('dcrs', 'impact') && Schema::hasColumn('dcrs', 'priority') && !in_array('idx_dcrs_impact_priority', $indexes)) {
                $table->index(['impact', 'priority'], 'idx_dcrs_impact_priority');
            }
        });

        // Add indexes to users table
        Schema::table('users', function (Blueprint $table) {
            $indexes = Schema::getIndexListing('users');
            
            if (!in_array('idx_users_email', $indexes)) $table->index('email', 'idx_users_email');
            if (!in_array('idx_users_is_active', $indexes) && Schema::hasColumn('users', 'is_active')) {
                $table->index('is_active', 'idx_users_is_active');
            }
            if (!in_array('idx_users_last_login', $indexes) && Schema::hasColumn('users', 'last_login_at')) {
                $table->index('last_login_at', 'idx_users_last_login');
            }
        });

        // Add indexes to notifications table if it exists
        if (Schema::hasTable('notifications')) {
            Schema::table('notifications', function (Blueprint $table) {
                $indexes = Schema::getIndexListing('notifications');
                $columns = Schema::getColumnListing('notifications');
                
                if (in_array('notifiable_id', $columns) && !in_array('idx_notifications_notifiable', $indexes)) {
                    $table->index('notifiable_id', 'idx_notifications_notifiable');
                }
                if (in_array('read_at', $columns) && !in_array('idx_notifications_read_at', $indexes)) {
                    $table->index('read_at', 'idx_notifications_read_at');
                }
                if (in_array('created_at', $columns) && !in_array('idx_notifications_created_at', $indexes)) {
                    $table->index('created_at', 'idx_notifications_created_at');
                }
                if (in_array('notifiable_id', $columns) && in_array('read_at', $columns) && !in_array('idx_notifications_notifiable_read', $indexes)) {
                    $table->index(['notifiable_id', 'read_at'], 'idx_notifications_notifiable_read');
                }
            });
        }

        // Add indexes to audit_logs table if it exists
        if (Schema::hasTable('audit_logs')) {
            Schema::table('audit_logs', function (Blueprint $table) {
                $indexes = Schema::getIndexListing('audit_logs');
                $columns = Schema::getColumnListing('audit_logs');
                
                if (in_array('user_id', $columns) && !in_array('idx_audit_logs_user', $indexes)) {
                    $table->index('user_id', 'idx_audit_logs_user');
                }
                if (in_array('event_type', $columns) && !in_array('idx_audit_logs_event_type', $indexes)) {
                    $table->index('event_type', 'idx_audit_logs_event_type');
                }
                if (in_array('event_timestamp', $columns) && !in_array('idx_audit_logs_timestamp', $indexes)) {
                    $table->index('event_timestamp', 'idx_audit_logs_timestamp');
                }
                if (in_array('resource_type', $columns) && in_array('resource_id', $columns) && !in_array('idx_audit_logs_resource', $indexes)) {
                    $table->index(['resource_type', 'resource_id'], 'idx_audit_logs_resource');
                }
            });
        }

        // Add indexes to documents table if it exists
        if (Schema::hasTable('documents')) {
            Schema::table('documents', function (Blueprint $table) {
                $indexes = Schema::getIndexListing('documents');
                $columns = Schema::getColumnListing('documents');
                
                if (in_array('change_request_id', $columns) && !in_array('idx_documents_dcr', $indexes)) {
                    $table->index('change_request_id', 'idx_documents_dcr');
                }
                if (in_array('document_type', $columns) && !in_array('idx_documents_type', $indexes)) {
                    $table->index('document_type', 'idx_documents_type');
                }
                if (in_array('uploaded_by', $columns) && !in_array('idx_documents_uploaded_by', $indexes)) {
                    $table->index('uploaded_by', 'idx_documents_uploaded_by');
                }
            });
        }

        // Add indexes to approvals table if it exists
        if (Schema::hasTable('approvals')) {
            Schema::table('approvals', function (Blueprint $table) {
                $indexes = Schema::getIndexListing('approvals');
                $columns = Schema::getColumnListing('approvals');
                
                if (in_array('change_request_id', $columns) && !in_array('idx_approvals_dcr', $indexes)) {
                    $table->index('change_request_id', 'idx_approvals_dcr');
                }
                if (in_array('approver_id', $columns) && !in_array('idx_approvals_approver', $indexes)) {
                    $table->index('approver_id', 'idx_approvals_approver');
                }
                if (in_array('decision', $columns) && !in_array('idx_approvals_decision', $indexes)) {
                    $table->index('decision', 'idx_approvals_decision');
                }
                if (in_array('decided_at', $columns) && !in_array('idx_approvals_decided_at', $indexes)) {
                    $table->index('decided_at', 'idx_approvals_decided_at');
                }
            });
        }

        // Add indexes to user_roles pivot table if it exists
        if (Schema::hasTable('user_roles')) {
            Schema::table('user_roles', function (Blueprint $table) {
                $indexes = Schema::getIndexListing('user_roles');
                $columns = Schema::getColumnListing('user_roles');
                
                if (in_array('user_id', $columns) && !in_array('idx_user_roles_user', $indexes)) {
                    $table->index('user_id', 'idx_user_roles_user');
                }
                if (in_array('role_id', $columns) && !in_array('idx_user_roles_role', $indexes)) {
                    $table->index('role_id', 'idx_user_roles_role');
                }
                if (in_array('is_active', $columns) && !in_array('idx_user_roles_is_active', $indexes)) {
                    $table->index('is_active', 'idx_user_roles_is_active');
                }
                if (in_array('expires_at', $columns) && !in_array('idx_user_roles_expires_at', $indexes)) {
                    $table->index('expires_at', 'idx_user_roles_expires_at');
                }
                if (in_array('user_id', $columns) && in_array('is_active', $columns) && !in_array('idx_user_roles_user_active', $indexes)) {
                    $table->index(['user_id', 'is_active'], 'idx_user_roles_user_active');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dcrs', function (Blueprint $table) {
            $table->dropIndex('idx_dcrs_author');
            $table->dropIndex('idx_dcrs_recipient');
            $table->dropIndex('idx_dcrs_decision_maker');
            $table->dropIndex('idx_dcrs_status');
            $table->dropIndex('idx_dcrs_created_at');
            $table->dropIndex('idx_dcrs_due_date');
            $table->dropIndex('idx_dcrs_impact');
            $table->dropIndex('idx_dcrs_priority');
            $table->dropIndex('idx_dcrs_auto_escalated');
            $table->dropIndex('idx_dcrs_status_created');
            $table->dropIndex('idx_dcrs_status_due');
            $table->dropIndex('idx_dcrs_recipient_status');
            $table->dropIndex('idx_dcrs_dm_status');
            $table->dropIndex('idx_dcrs_author_created');
            $table->dropIndex('idx_dcrs_impact_priority');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('idx_users_email');
            $table->dropIndex('idx_users_is_active');
            $table->dropIndex('idx_users_last_login');
        });

        if (Schema::hasTable('notifications')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->dropIndex('idx_notifications_notifiable');
                $table->dropIndex('idx_notifications_read_at');
                $table->dropIndex('idx_notifications_created_at');
                $table->dropIndex('idx_notifications_notifiable_read');
            });
        }

        if (Schema::hasTable('audit_logs')) {
            Schema::table('audit_logs', function (Blueprint $table) {
                $table->dropIndex('idx_audit_logs_user');
                $table->dropIndex('idx_audit_logs_event_type');
                $table->dropIndex('idx_audit_logs_timestamp');
                $table->dropIndex('idx_audit_logs_resource');
            });
        }

        if (Schema::hasTable('documents')) {
            Schema::table('documents', function (Blueprint $table) {
                $table->dropIndex('idx_documents_dcr');
                $table->dropIndex('idx_documents_type');
                $table->dropIndex('idx_documents_uploaded_by');
            });
        }

        if (Schema::hasTable('approvals')) {
            Schema::table('approvals', function (Blueprint $table) {
                $table->dropIndex('idx_approvals_dcr');
                $table->dropIndex('idx_approvals_approver');
                $table->dropIndex('idx_approvals_decision');
                $table->dropIndex('idx_approvals_decided_at');
            });
        }

        if (Schema::hasTable('user_roles')) {
            Schema::table('user_roles', function (Blueprint $table) {
                $table->dropIndex('idx_user_roles_user');
                $table->dropIndex('idx_user_roles_role');
                $table->dropIndex('idx_user_roles_is_active');
                $table->dropIndex('idx_user_roles_expires_at');
                $table->dropIndex('idx_user_roles_user_active');
            });
        }
    }
};
