<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop views if they exist
        DB::statement("DROP VIEW IF EXISTS v_active_change_requests");
        DB::statement("DROP VIEW IF EXISTS v_audit_summary");
        DB::statement("DROP VIEW IF EXISTS v_user_activity_summary");
        DB::statement("DROP VIEW IF EXISTS v_dcr_performance_metrics");
        DB::statement("DROP VIEW IF EXISTS v_impact_analysis");
        DB::statement("DROP VIEW IF EXISTS v_notification_summary");
        DB::statement("DROP VIEW IF EXISTS v_document_statistics");
        DB::statement("DROP VIEW IF EXISTS v_escalation_analysis");

        // View for Active Change Requests with User Details
        DB::statement("
            CREATE VIEW v_active_change_requests AS
            SELECT 
                cr.*,
                author.name as author_name,
                author.email as author_email,
                recipient.name as recipient_name,
                recipient.email as recipient_email,
                dom.name as decision_maker_name,
                dom.email as decision_maker_email,
                ia.impact_rating as assessment_impact_rating,
                ia.business_impact,
                ia.risk_level,
                DATEDIFF(cr.due_date, CURDATE()) as days_until_due,
                CASE 
                    WHEN cr.due_date < CURDATE() THEN 1 
                    ELSE 0 
                END as is_overdue
            FROM dcrs cr
            LEFT JOIN users author ON cr.author_id = author.id
            LEFT JOIN users recipient ON cr.recipient_id = recipient.id
            LEFT JOIN users dom ON cr.decision_maker_id = dom.id
            LEFT JOIN impact_assessments ia ON cr.id = ia.change_request_id
            WHERE cr.status NOT IN ('Closed', 'Cancelled')
        ");

        // View for Audit Summary
        DB::statement("
            CREATE VIEW v_audit_summary AS
            SELECT 
                event_category,
                event_type,
                COUNT(*) as event_count,
                COUNT(CASE WHEN success = FALSE THEN 1 END) as failure_count,
                DATE(event_timestamp) as event_date
            FROM audit_logs
            WHERE event_timestamp >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
            GROUP BY event_category, event_type, DATE(event_timestamp)
        ");

        // View for User Activity Summary
        DB::statement("
            CREATE VIEW v_user_activity_summary AS
            SELECT 
                u.id,
                u.name,
                u.email,
                COUNT(DISTINCT cr.id) as total_dcrs_submitted,
                COUNT(DISTINCT cr_assigned.id) as total_dcrs_assigned,
                COUNT(DISTINCT cr_approved.id) as total_dcrs_approved,
                COUNT(DISTINCT cr_completed.id) as total_dcrs_completed,
                COUNT(DISTINCT al.id) as total_audit_events,
                u.last_login_at,
                u.is_active
            FROM users u
            LEFT JOIN dcrs cr ON u.id = cr.author_id
            LEFT JOIN dcrs cr_assigned ON u.id = cr_assigned.recipient_id
            LEFT JOIN dcrs cr_approved ON u.id = cr_approved.decision_maker_id AND cr_approved.status = 'Approved'
            LEFT JOIN dcrs cr_completed ON u.id = cr_completed.recipient_id AND cr_completed.status = 'Completed'
            LEFT JOIN audit_logs al ON u.id = al.user_id
            GROUP BY u.id, u.name, u.email, u.last_login_at, u.is_active
        ");

        // View for DCR Performance Metrics
        DB::statement("
            CREATE VIEW v_dcr_performance_metrics AS
            SELECT 
                DATE(cr.created_at) as date,
                COUNT(*) as total_created,
                COUNT(CASE WHEN cr.status = 'Approved' THEN 1 END) as total_approved,
                COUNT(CASE WHEN cr.status = 'Completed' THEN 1 END) as total_completed,
                COUNT(CASE WHEN cr.status = 'Closed' THEN 1 END) as total_closed,
                0 as avg_approval_time_days,
                0 as avg_implementation_time_days,
                0 as avg_closure_time_days,
                0 as total_escalated
            FROM dcrs cr
            WHERE cr.created_at >= DATE_SUB(CURDATE(), INTERVAL 90 DAY)
            GROUP BY DATE(cr.created_at)
            ORDER BY date DESC
        ");

        // View for Impact Analysis
        DB::statement("
            CREATE VIEW v_impact_analysis AS
            SELECT 
                ia.impact_rating,
                ia.business_impact,
                ia.technical_impact,
                ia.risk_level,
                COUNT(*) as total_assessments,
                COUNT(CASE WHEN cr.status = 'Approved' THEN 1 END) as approved_count,
                COUNT(CASE WHEN cr.status = 'Rejected' THEN 1 END) as rejected_count,
                0 as escalated_count,
                0 as avg_approval_time_days
            FROM impact_assessments ia
            LEFT JOIN dcrs cr ON ia.change_request_id = cr.id
            GROUP BY ia.impact_rating, ia.business_impact, ia.technical_impact, ia.risk_level
        ");

        // View for Notification Summary
        DB::statement("
            CREATE VIEW v_notification_summary AS
            SELECT 
                type,
                category,
                status,
                priority,
                COUNT(*) as total_count,
                0 as read_count,
                COUNT(CASE WHEN status = 'Failed' THEN 1 END) as failed_count,
                0 as avg_read_time_hours
            FROM notifications
            WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
            GROUP BY type, category, status, priority
        ");

        // View for Document Statistics
        DB::statement("
            CREATE VIEW v_document_statistics AS
            SELECT 
                document_type,
                access_level,
                COUNT(*) as total_documents,
                SUM(file_size) as total_file_size,
                AVG(file_size) as avg_file_size,
                SUM(download_count) as total_downloads,
                COUNT(DISTINCT uploaded_by) as unique_uploaders,
                COUNT(DISTINCT change_request_id) as dcrs_with_documents
            FROM documents
            GROUP BY document_type, access_level
        ");

        // View for Escalation Analysis
        DB::statement("
            CREATE VIEW v_escalation_analysis AS
            SELECT 
                ia.impact_rating,
                COUNT(*) as total_dcrs,
                0 as auto_escalated_count,
                0 as total_escalated_count,
                0 as avg_days_to_escalation,
                0 as approved_after_escalation
            FROM dcrs cr
            LEFT JOIN impact_assessments ia ON cr.id = ia.change_request_id
            GROUP BY ia.impact_rating
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop views in reverse order of creation
        DB::statement("DROP VIEW IF EXISTS v_escalation_analysis");
        DB::statement("DROP VIEW IF EXISTS v_document_statistics");
        DB::statement("DROP VIEW IF EXISTS v_notification_summary");
        DB::statement("DROP VIEW IF EXISTS v_impact_analysis");
        DB::statement("DROP VIEW IF EXISTS v_dcr_performance_metrics");
        DB::statement("DROP VIEW IF EXISTS v_user_activity_summary");
        DB::statement("DROP VIEW IF EXISTS v_audit_summary");
        DB::statement("DROP VIEW IF EXISTS v_active_change_requests");
    }
};
