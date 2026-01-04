<?php

namespace Database\Seeders;

use App\Models\Dcr;
use App\Models\User;
use App\Models\ImpactAssessment;
use App\Models\Approval;
use App\Models\Document;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SampleDcrSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get users
        $johnAuthor = User::where('email', 'john.author@example.com')->first();
        $janeAuthor = User::where('email', 'jane.author@example.com')->first();
        $mikeRecipient = User::where('email', 'mike.recipient@example.com')->first();
        $sarahRecipient = User::where('email', 'sarah.recipient@example.com')->first();
        $davidDom = User::where('email', 'david.dom@example.com')->first();
        $lisaDom = User::where('email', 'lisa.dom@example.com')->first();

        // Sample DCRs
        $dcrs = [
            [
                'title' => 'Upgrade Financial Reporting System',
                'description' => 'Upgrade the current financial reporting system to the latest version to improve performance and add new features.',
                'reason_for_change' => 'Current system is outdated and lacks required compliance features.',
                'request_type' => 'Standard',
                'priority' => 'High',
                'author' => $johnAuthor,
                'recipient' => $mikeRecipient,
                'decision_maker' => $davidDom,
                'due_date' => now()->addDays(7),
                'status' => 'Approved',
                'impact_rating' => 'High',
                'business_impact' => 'High',
                'technical_impact' => 'Medium',
                'risk_level' => 'Medium',
            ],
            [
                'title' => 'Implement New Backup Solution',
                'description' => 'Deploy a new automated backup solution for critical business data.',
                'reason_for_change' => 'Current backup process is manual and error-prone.',
                'request_type' => 'Standard',
                'priority' => 'Medium',
                'author' => $janeAuthor,
                'recipient' => $sarahRecipient,
                'decision_maker' => $lisaDom,
                'due_date' => now()->addDays(14),
                'status' => 'In_Review',
                'impact_rating' => 'Medium',
                'business_impact' => 'Medium',
                'technical_impact' => 'Medium',
                'risk_level' => 'Low',
            ],
            [
                'title' => 'Update Security Policies',
                'description' => 'Review and update all IT security policies to meet new compliance requirements.',
                'reason_for_change' => 'New regulatory requirements require updated security policies.',
                'request_type' => 'Routine',
                'priority' => 'Low',
                'author' => $johnAuthor,
                'recipient' => $mikeRecipient,
                'decision_maker' => $davidDom,
                'due_date' => now()->addDays(21),
                'status' => 'Pending',
                'impact_rating' => 'Low',
                'business_impact' => 'Low',
                'technical_impact' => 'Low',
                'risk_level' => 'Low',
            ],
        ];

        foreach ($dcrs as $dcrData) {
            // Create DCR
            $dcr = Dcr::create([
                'dcr_id' => 'DCR-' . strtoupper(Str::random(6)),
                'request_type' => $dcrData['request_type'],
                'reason_for_change' => $dcrData['reason_for_change'],
                'due_date' => $dcrData['due_date'],
                'status' => strtolower($dcrData['status']),
                'impact' => $dcrData['impact_rating'],
                'impact_summary' => 'This change will affect multiple systems and requires careful planning.',
                'recommendations' => 'Implement during off-hours to minimize disruption.',
                'author_id' => $dcrData['author']->id,
                'recipient_id' => $dcrData['recipient']->id,
                'decision_maker_id' => $dcrData['decision_maker']->id,
            ]);

            // Create impact assessment
            ImpactAssessment::create([
                'change_request_id' => $dcr->id,
                'assessor_id' => $dcrData['decision_maker']->id,
                'impact_rating' => $dcrData['impact_rating'],
                'business_impact' => $dcrData['business_impact'],
                'technical_impact' => $dcrData['technical_impact'],
                'risk_level' => $dcrData['risk_level'],
                'impact_summary' => 'This change will affect multiple systems and requires careful planning.',
                'affected_systems' => ['Financial System', 'Reporting Module', 'Database'],
                'affected_users' => ['Finance Team', 'Management', 'Auditors'],
                'downtime_estimate' => '2-4 hours',
                'rollback_plan' => 'System snapshots will be created before implementation.',
                'testing_requirements' => 'Unit tests, integration tests, and user acceptance testing required.',
                'assessment_date' => now()->subDays(1),
                'confidence_level' => 'High',
            ]);

            // Create approval if status is Approved
            // Temporarily commented out due to timestamp issues
            // if ($dcrData['status'] === 'Approved') {
            //     Approval::create([
            //         'change_request_id' => $dcr->id,
            //         'approver_id' => $dcrData['decision_maker']->id,
            //         'decision' => 'Approved',
            //         'approval_level' => 'Final',
            //         'decision_reason' => 'Change approved after thorough impact assessment. All requirements met.',
            //         'sequence_order' => 1,
            //         'is_final' => true,
            //         'decided_at' => now()->subHours(6),
            //         'ip_address' => '192.168.1.100',
            //     ]);
            // }

            // Create sample document
            // Temporarily commented out due to timestamp issues
            // Document::create([
            //     'change_request_id' => $dcr->id,
            //     'document_type' => 'Attachment',
            //     'title' => 'Technical Specification',
            //     'description' => 'Detailed technical specification for the proposed change.',
            //     'original_filename' => 'tech_spec.pdf',
            //     'stored_filename' => 'tech_spec_' . $dcr->id . '.pdf',
            //     'file_path' => 'documents/dcr_' . $dcr->id,
            //     'file_size' => 1024000, // 1MB
            //     'mime_type' => 'application/pdf',
            //     'file_hash' => hash('sha256', 'tech_spec_' . $dcr->id),
            //     'uploaded_by' => $dcrData['author']->id,
            // ]);

            $this->command->info("Created DCR: {$dcrData['title']}");
        }

        $this->command->info('Sample DCRs created successfully!');
    }
}
