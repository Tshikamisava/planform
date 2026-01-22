<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\ChangeRequest;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DcrDemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create demo users if they don't exist
        $users = [
            [
                'name' => 'Alex Engineer',
                'email' => 'alex.engineer@planform.com',
                'password' => Hash::make('password'),
                'is_active' => true,
            ],
            [
                'name' => 'Sarah Lee',
                'email' => 'sarah.lee@planform.com',
                'password' => Hash::make('password'),
                'is_active' => true,
            ],
            [
                'name' => 'Mike Chen',
                'email' => 'mike.chen@planform.com',
                'password' => Hash::make('password'),
                'is_active' => true,
            ],
            [
                'name' => 'John Doe',
                'email' => 'john.doe@planform.com',
                'password' => Hash::make('password'),
                'is_active' => true,
            ],
            [
                'name' => 'QA Manager',
                'email' => 'qa.manager@planform.com',
                'password' => Hash::make('password'),
                'is_active' => true,
            ],
        ];

        $createdUsers = [];
        foreach ($users as $userData) {
            $createdUsers[$userData['name']] = User::firstOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }

        // Create demo DCRs
        $dcrs = [
            [
                'dcr_id' => 'D-101',
                'title' => 'Bracing Update for XJ-200',
                'description' => 'Structural reinforcement to prevent vibration fatigue',
                'author_id' => $createdUsers['Alex Engineer']->id,
                'recipient_id' => $createdUsers['Sarah Lee']->id,
                'decision_maker_id' => $createdUsers['John Doe']->id,
                'request_type' => 'Safety',
                'reason_for_change' => 'Strengthening the side wall by 2mm to prevent vibration fatigue observed during field testing. Detailed analysis shows that increasing wall thickness will significantly improve structural integrity without major cost impact.',
                'due_date' => Carbon::parse('2023-11-15'),
                'status' => 'Preliminary',
                'impact' => 'Medium',
                'impact_summary' => 'Cost: R0.50, Weight: +0.12kg, Tooling update required',
                'recommendations' => 'Modify mold cavity #4',
                'attachments' => json_encode(['Spec_v2.pdf', 'CAD_Pack.zip']),
                'created_at' => Carbon::parse('2023-10-25'),
            ],
            [
                'dcr_id' => 'D-104',
                'title' => 'Chassis Material Swap',
                'description' => 'Aluminum alloy change for improved performance',
                'author_id' => $createdUsers['John Doe']->id,
                'recipient_id' => $createdUsers['QA Manager']->id,
                'decision_maker_id' => $createdUsers['John Doe']->id,
                'request_type' => 'Improvement',
                'reason_for_change' => 'Switching from Al-6061 to Al-7075 for better weight-to-strength ratio. Material analysis indicates significant performance gains with minimal cost increase.',
                'due_date' => Carbon::parse('2023-10-29'),
                'status' => 'Detailed',
                'impact' => 'High',
                'impact_summary' => 'Cost: R2.10, Weight: -0.45kg, Inventory scrap required',
                'recommendations' => 'Approve material substitution and update BOM',
                'attachments' => json_encode(['Material_Sheet.pdf']),
                'created_at' => Carbon::parse('2023-10-20'),
            ],
            [
                'dcr_id' => 'D-099',
                'title' => 'Welding Defect Correction',
                'description' => 'Frame assembly weld joint repair procedure',
                'author_id' => $createdUsers['Sarah Lee']->id,
                'recipient_id' => $createdUsers['Mike Chen']->id,
                'decision_maker_id' => $createdUsers['John Doe']->id,
                'request_type' => 'Defect',
                'reason_for_change' => 'Fixing welding defect in frame assembly. Field reports indicate weld joint failure under stress testing.',
                'due_date' => now()->addDays(10),
                'status' => 'Approved',
                'impact' => 'Low',
                'impact_summary' => 'Cost: R0.25, Weight: 0kg',
                'recommendations' => 'Implement revised welding procedure',
                'attachments' => json_encode(['Weld_Analysis.pdf']),
                'created_at' => now()->subDays(15),
            ],
            [
                'dcr_id' => 'D-105',
                'title' => 'Color Change Request',
                'description' => 'Exterior panel color specification update',
                'author_id' => $createdUsers['Mike Chen']->id,
                'recipient_id' => $createdUsers['Alex Engineer']->id,
                'decision_maker_id' => $createdUsers['John Doe']->id,
                'request_type' => 'Customer Request',
                'reason_for_change' => 'Color change for exterior panels as per customer specification. Customer requested RAL 5010 Gentian Blue instead of current RAL 7035',
                'due_date' => now()->addDays(20),
                'status' => 'Pending',
                'impact' => 'Low',
                'impact_summary' => 'Cost: R0.15, Weight: 0kg',
                'recommendations' => 'Update painting specification',
                'attachments' => json_encode(['Color_Spec.pdf']),
                'created_at' => now()->subDays(5),
            ],
            [
                'dcr_id' => 'D-106',
                'title' => 'Bearing Housing Redesign',
                'description' => 'Friction reduction optimization for bearing assembly',
                'author_id' => $createdUsers['Alex Engineer']->id,
                'recipient_id' => $createdUsers['Sarah Lee']->id,
                'decision_maker_id' => $createdUsers['John Doe']->id,
                'request_type' => 'Improvement',
                'reason_for_change' => 'Optimize bearing housing design for reduced friction. New bearing housing geometry reduces friction by 15% in lab testing',
                'due_date' => now()->addDays(30),
                'status' => 'In_Progress',
                'impact' => 'Medium',
                'impact_summary' => 'Cost: R1.20, Weight: -0.08kg',
                'recommendations' => 'Proceed with prototyping',
                'attachments' => json_encode(['Bearing_Design.pdf', 'Test_Results.xlsx']),
                'created_at' => now()->subDays(3),
            ],
        ];

        $createdDcrs = [];
        foreach ($dcrs as $dcrData) {
            $dcr = ChangeRequest::firstOrCreate(
                ['dcr_id' => $dcrData['dcr_id']],
                $dcrData
            );
            $createdDcrs[] = $dcr;
        }

        // Create audit logs for DCRs
        $auditLogs = [
            [
                'uuid' => Str::uuid(),
                'event_type' => 'info',
                'event_category' => 'Workflow',
                'action' => 'transitioned to Preliminary Design',
                'user_id' => $createdUsers['Sarah Lee']->id,
                'resource_type' => 'ChangeRequest',
                'resource_id' => $createdDcrs[0]->id,
                'ip_address' => '127.0.0.1',
                'created_at' => now()->subHours(2),
            ],
            [
                'uuid' => Str::uuid(),
                'event_type' => 'approved',
                'event_category' => 'Workflow',
                'action' => 'approved Conceptual phase',
                'user_id' => $createdUsers['Alex Engineer']->id,
                'resource_type' => 'ChangeRequest',
                'resource_id' => $createdDcrs[0]->id,
                'ip_address' => '127.0.0.1',
                'created_at' => now()->subDay(),
            ],
            [
                'uuid' => Str::uuid(),
                'event_type' => 'info',
                'event_category' => 'Workflow',
                'action' => 'Detailed Design & Procurement phase started',
                'user_id' => null,
                'resource_type' => 'ChangeRequest',
                'resource_id' => $createdDcrs[1]->id,
                'ip_address' => '127.0.0.1',
                'created_at' => now()->subDays(2),
            ],
            [
                'uuid' => Str::uuid(),
                'event_type' => 'approved',
                'event_category' => 'Workflow',
                'action' => 'approved D-099 Handover',
                'user_id' => $createdUsers['John Doe']->id,
                'resource_type' => 'ChangeRequest',
                'resource_id' => $createdDcrs[2]->id,
                'ip_address' => '127.0.0.1',
                'created_at' => now()->subMinutes(10),
            ],
            [
                'uuid' => Str::uuid(),
                'event_type' => 'info',
                'event_category' => 'Workflow',
                'action' => 'pushed D-101 to Preliminary Design',
                'user_id' => $createdUsers['Sarah Lee']->id,
                'resource_type' => 'ChangeRequest',
                'resource_id' => $createdDcrs[0]->id,
                'ip_address' => '127.0.0.1',
                'created_at' => now()->subMinutes(45),
            ],
        ];

        foreach ($auditLogs as $logData) {
            AuditLog::create($logData);
        }

        $this->command->info('Demo data seeded successfully!');
        $this->command->info('Users created: ' . count($users));
        $this->command->info('DCRs created: ' . count($dcrs));
        $this->command->info('Audit logs created: ' . count($auditLogs));
        $this->command->info('');
        $this->command->info('Demo login credentials:');
        $this->command->info('Email: alex.engineer@planform.com');
        $this->command->info('Password: password');
    }
}
