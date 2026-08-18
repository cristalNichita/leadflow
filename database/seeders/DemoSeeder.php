<?php

namespace Database\Seeders;

use App\Enums\CustomerStatus;
use App\Enums\DealStatus;
use App\Enums\LeadStatus;
use App\Enums\TaskPriority;
use App\Enums\UserRole;
use App\Models\Activity;
use App\Models\Customer;
use App\Models\Deal;
use App\Models\Lead;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::create([
            'name' => 'Alex Morgan',
            'email' => 'admin@leadflow.test',
            'password' => Hash::make('password'),
            'role' => UserRole::Admin,
            'email_verified_at' => now(),
        ]);

        $manager = User::create([
            'name' => 'Emma Collins',
            'email' => 'manager@leadflow.test',
            'password' => Hash::make('password'),
            'role' => UserRole::Manager,
            'email_verified_at' => now(),
        ]);

        $sarah = User::create([
            'name' => 'Sarah Mitchell',
            'email' => 'sarah@leadflow.test',
            'password' => Hash::make('password'),
            'role' => UserRole::User,
            'email_verified_at' => now(),
        ]);

        $daniel = User::create([
            'name' => 'Daniel Brooks',
            'email' => 'daniel@leadflow.test',
            'password' => Hash::make('password'),
            'role' => UserRole::User,
            'email_verified_at' => now(),
        ]);

        $olivia = User::create([
            'name' => 'Olivia Parker',
            'email' => 'olivia@leadflow.test',
            'password' => Hash::make('password'),
            'role' => UserRole::User,
            'email_verified_at' => now(),
        ]);

        $customers = collect([
            [
                'name' => 'Oliver Bennett',
                'company' => 'Northstar Digital',
                'email' => 'oliver@northstar.test',
                'phone' => '+1 415 555 0142',
                'status' => CustomerStatus::Active,
                'notes' => 'Growing digital agency exploring a long-term development partnership.',
            ],
            [
                'name' => 'Sophia Carter',
                'company' => 'BrightPeak Studio',
                'email' => 'sophia@brightpeak.test',
                'phone' => '+1 212 555 0187',
                'status' => CustomerStatus::Active,
                'notes' => 'Interested in replacing several internal spreadsheets with a CRM.',
            ],
            [
                'name' => 'Ethan Walker',
                'company' => 'Vertex Systems',
                'email' => 'ethan@vertex.test',
                'phone' => '+44 20 7946 0241',
                'status' => CustomerStatus::Active,
                'notes' => 'SaaS company. High potential for recurring support work.',
            ],
            [
                'name' => 'Mia Thompson',
                'company' => 'Bluewave Media',
                'email' => 'mia@bluewave.test',
                'phone' => '+1 646 555 0109',
                'status' => CustomerStatus::Active,
                'notes' => 'Marketing agency looking for dashboard and reporting improvements.',
            ],
            [
                'name' => 'Noah Anderson',
                'company' => 'Oakline Finance',
                'email' => 'noah@oakline.test',
                'phone' => '+1 312 555 0174',
                'status' => CustomerStatus::Active,
                'notes' => 'Requires secure integrations with several third-party systems.',
            ],
            [
                'name' => 'Ava Richardson',
                'company' => 'Luma Commerce',
                'email' => 'ava@luma.test',
                'phone' => '+1 206 555 0126',
                'status' => CustomerStatus::Active,
                'notes' => 'E-commerce company preparing for international expansion.',
            ],
            [
                'name' => 'James Foster',
                'company' => 'Atlas Logistics',
                'email' => 'james@atlas.test',
                'phone' => '+44 20 7946 0355',
                'status' => CustomerStatus::Active,
                'notes' => 'Needs improvements to their operations dashboard.',
            ],
            [
                'name' => 'Isabella Reed',
                'company' => 'NovaHealth',
                'email' => 'isabella@novahealth.test',
                'phone' => '+1 617 555 0190',
                'status' => CustomerStatus::Active,
                'notes' => 'Initial discovery completed.',
            ],
            [
                'name' => 'Lucas Morgan',
                'company' => 'Craftlane',
                'email' => 'lucas@craftlane.test',
                'phone' => '+1 503 555 0158',
                'status' => CustomerStatus::Inactive,
                'notes' => 'Project paused until the next funding round.',
            ],
            [
                'name' => 'Charlotte Evans',
                'company' => 'Evergreen Legal',
                'email' => 'charlotte@evergreen.test',
                'phone' => '+1 202 555 0164',
                'status' => CustomerStatus::Active,
                'notes' => 'Interested in automating document workflows.',
            ],
            [
                'name' => 'Henry Wilson',
                'company' => 'PixelForge',
                'email' => 'henry@pixelforge.test',
                'phone' => '+1 213 555 0115',
                'status' => CustomerStatus::Active,
                'notes' => 'Referral from an existing partner.',
            ],
            [
                'name' => 'Amelia Scott',
                'company' => 'Harbor Analytics',
                'email' => 'amelia@harbor.test',
                'phone' => '+1 617 555 0133',
                'status' => CustomerStatus::Inactive,
                'notes' => 'No active opportunities at the moment.',
            ],
        ])->map(
            fn (array $data): Customer => Customer::create($data),
        );

        $northstar = $customers[0];
        $brightPeak = $customers[1];
        $vertex = $customers[2];
        $bluewave = $customers[3];
        $oakline = $customers[4];
        $luma = $customers[5];
        $atlas = $customers[6];
        $novaHealth = $customers[7];
        $evergreen = $customers[9];
        $pixelForge = $customers[10];

        $leads = collect([
            [
                'title' => 'Website redesign platform',
                'customer_id' => $northstar->id,
                'assigned_user_id' => $sarah->id,
                'estimated_value' => 8500,
                'source' => 'Referral',
                'status' => LeadStatus::Qualified,
                'notes' => 'Discovery call completed. Preparing detailed proposal.',
            ],
            [
                'title' => 'Internal CRM implementation',
                'customer_id' => $brightPeak->id,
                'assigned_user_id' => $daniel->id,
                'estimated_value' => 14000,
                'source' => 'Website',
                'status' => LeadStatus::Contacted,
                'notes' => 'Client wants a first prototype before committing.',
            ],
            [
                'title' => 'Customer portal rebuild',
                'customer_id' => $vertex->id,
                'assigned_user_id' => $sarah->id,
                'estimated_value' => 22000,
                'source' => 'LinkedIn',
                'status' => LeadStatus::Qualified,
                'notes' => 'Technical requirements received.',
            ],
            [
                'title' => 'Analytics dashboard',
                'customer_id' => $bluewave->id,
                'assigned_user_id' => $olivia->id,
                'estimated_value' => 7500,
                'source' => 'Website',
                'status' => LeadStatus::New,
                'notes' => 'Inbound lead from landing page.',
            ],
            [
                'title' => 'Payment API integration',
                'customer_id' => $oakline->id,
                'assigned_user_id' => $daniel->id,
                'estimated_value' => 18000,
                'source' => 'Conference',
                'status' => LeadStatus::Contacted,
                'notes' => 'Security requirements need additional review.',
            ],
            [
                'title' => 'Commerce operations dashboard',
                'customer_id' => $luma->id,
                'assigned_user_id' => $olivia->id,
                'estimated_value' => 11500,
                'source' => 'Referral',
                'status' => LeadStatus::Won,
                'notes' => 'Converted into an active deal.',
            ],
            [
                'title' => 'Fleet tracking improvements',
                'customer_id' => $atlas->id,
                'assigned_user_id' => $sarah->id,
                'estimated_value' => 16000,
                'source' => 'Email',
                'status' => LeadStatus::Qualified,
                'notes' => 'Proposal requested for the next planning meeting.',
            ],
            [
                'title' => 'Patient portal prototype',
                'customer_id' => $novaHealth->id,
                'assigned_user_id' => $daniel->id,
                'estimated_value' => 20000,
                'source' => 'Referral',
                'status' => LeadStatus::Lost,
                'notes' => 'Budget postponed until next year.',
            ],
            [
                'title' => 'Document workflow automation',
                'customer_id' => $evergreen->id,
                'assigned_user_id' => $olivia->id,
                'estimated_value' => 9500,
                'source' => 'Website',
                'status' => LeadStatus::New,
                'notes' => 'Waiting for discovery meeting.',
            ],
            [
                'title' => 'Client reporting portal',
                'customer_id' => $pixelForge->id,
                'assigned_user_id' => $sarah->id,
                'estimated_value' => 13000,
                'source' => 'Referral',
                'status' => LeadStatus::Contacted,
                'notes' => 'Initial scope sent by email.',
            ],
        ])->map(
            fn (array $data): Lead => Lead::create($data),
        );

        $deals = collect([
            [
                'title' => 'Northstar development retainer',
                'customer_id' => $northstar->id,
                'assigned_user_id' => $sarah->id,
                'value' => 24000,
                'status' => DealStatus::Open,
                'expected_close_date' => now()
                    ->addDays(18)
                    ->toDateString(),
                'notes' => 'Final commercial terms under review.',
            ],
            [
                'title' => 'Vertex portal modernization',
                'customer_id' => $vertex->id,
                'assigned_user_id' => $sarah->id,
                'value' => 32000,
                'status' => DealStatus::Open,
                'expected_close_date' => now()
                    ->addDays(28)
                    ->toDateString(),
                'notes' => 'Technical proposal approved.',
            ],
            [
                'title' => 'Luma operations dashboard',
                'customer_id' => $luma->id,
                'assigned_user_id' => $olivia->id,
                'value' => 14500,
                'status' => DealStatus::Won,
                'expected_close_date' => now()
                    ->subDays(8)
                    ->toDateString(),
                'notes' => 'Contract signed and kickoff completed.',
            ],
            [
                'title' => 'Bluewave reporting suite',
                'customer_id' => $bluewave->id,
                'assigned_user_id' => $olivia->id,
                'value' => 9800,
                'status' => DealStatus::Open,
                'expected_close_date' => now()
                    ->addDays(12)
                    ->toDateString(),
                'notes' => 'Waiting for stakeholder approval.',
            ],
            [
                'title' => 'Oakline integration package',
                'customer_id' => $oakline->id,
                'assigned_user_id' => $daniel->id,
                'value' => 27500,
                'status' => DealStatus::Won,
                'expected_close_date' => now()
                    ->subDays(20)
                    ->toDateString(),
                'notes' => 'Phase one completed successfully.',
            ],
            [
                'title' => 'Atlas operations portal',
                'customer_id' => $atlas->id,
                'assigned_user_id' => $sarah->id,
                'value' => 19000,
                'status' => DealStatus::Open,
                'expected_close_date' => now()
                    ->addDays(35)
                    ->toDateString(),
                'notes' => 'Awaiting procurement review.',
            ],
            [
                'title' => 'Evergreen workflow automation',
                'customer_id' => $evergreen->id,
                'assigned_user_id' => $olivia->id,
                'value' => 12500,
                'status' => DealStatus::Lost,
                'expected_close_date' => now()
                    ->subDays(14)
                    ->toDateString(),
                'notes' => 'Client selected an internal solution.',
            ],
            [
                'title' => 'PixelForge reporting system',
                'customer_id' => $pixelForge->id,
                'assigned_user_id' => $daniel->id,
                'value' => 17000,
                'status' => DealStatus::Open,
                'expected_close_date' => now()
                    ->addDays(22)
                    ->toDateString(),
                'notes' => 'Second proposal revision requested.',
            ],
        ])->map(
            fn (array $data): Deal => Deal::create($data),
        );

        $tasks = collect([
            [
                'title' => 'Send Northstar final proposal',
                'description' => 'Finalize pricing and send the proposal before the next stakeholder call.',
                'assigned_user_id' => $sarah->id,
                'deal_id' => $deals[0]->id,
                'priority' => TaskPriority::High,
                'due_date' => now()
                    ->addDays(2)
                    ->toDateString(),
                'completed' => false,
            ],
            [
                'title' => 'Schedule BrightPeak discovery call',
                'description' => 'Confirm available times with Sophia and the operations lead.',
                'assigned_user_id' => $daniel->id,
                'customer_id' => $brightPeak->id,
                'priority' => TaskPriority::Medium,
                'due_date' => now()
                    ->addDays(4)
                    ->toDateString(),
                'completed' => false,
            ],
            [
                'title' => 'Review Vertex API documentation',
                'description' => 'Check authentication requirements before implementation planning.',
                'assigned_user_id' => $sarah->id,
                'deal_id' => $deals[1]->id,
                'priority' => TaskPriority::High,
                'due_date' => now()
                    ->addDay()
                    ->toDateString(),
                'completed' => false,
            ],
            [
                'title' => 'Prepare Bluewave dashboard mockup',
                'description' => 'Create a first dashboard structure for the stakeholder review.',
                'assigned_user_id' => $olivia->id,
                'deal_id' => $deals[3]->id,
                'priority' => TaskPriority::Medium,
                'due_date' => now()
                    ->addDays(6)
                    ->toDateString(),
                'completed' => false,
            ],
            [
                'title' => 'Follow up with Oakline',
                'description' => 'Discuss requirements for phase two of the integration.',
                'assigned_user_id' => $daniel->id,
                'customer_id' => $oakline->id,
                'priority' => TaskPriority::Low,
                'due_date' => now()
                    ->addDays(9)
                    ->toDateString(),
                'completed' => false,
            ],
            [
                'title' => 'Send Atlas revised estimate',
                'description' => 'Include the additional reporting module requested last week.',
                'assigned_user_id' => $sarah->id,
                'deal_id' => $deals[5]->id,
                'priority' => TaskPriority::High,
                'due_date' => now()
                    ->subDay()
                    ->toDateString(),
                'completed' => false,
            ],
            [
                'title' => 'Check Evergreen decision',
                'description' => 'Close remaining notes after the lost deal.',
                'assigned_user_id' => $olivia->id,
                'customer_id' => $evergreen->id,
                'priority' => TaskPriority::Low,
                'due_date' => now()
                    ->subDays(5)
                    ->toDateString(),
                'completed' => true,
            ],
            [
                'title' => 'Prepare PixelForge technical scope',
                'description' => 'Document the reporting API and authentication work.',
                'assigned_user_id' => $daniel->id,
                'deal_id' => $deals[7]->id,
                'priority' => TaskPriority::Medium,
                'due_date' => now()
                    ->addDays(7)
                    ->toDateString(),
                'completed' => false,
            ],
            [
                'title' => 'Archive Luma kickoff notes',
                'description' => 'Move approved discovery notes into the project workspace.',
                'assigned_user_id' => $olivia->id,
                'customer_id' => $luma->id,
                'priority' => TaskPriority::Low,
                'due_date' => now()
                    ->subDays(2)
                    ->toDateString(),
                'completed' => true,
            ],
            [
                'title' => 'Contact new Bluewave lead',
                'description' => 'Introduce the team and clarify reporting requirements.',
                'assigned_user_id' => $olivia->id,
                'customer_id' => $bluewave->id,
                'priority' => TaskPriority::High,
                'due_date' => now()
                    ->addDays(3)
                    ->toDateString(),
                'completed' => false,
            ],
        ])->map(
            fn (array $data): Task => Task::create($data),
        );

        $activities = [
            [
                $manager,
                'Emma Collins created customer "Northstar Digital"',
                now()->subDays(6)->subHours(3),
            ],
            [
                $sarah,
                'Sarah Mitchell created lead "Website redesign platform"',
                now()->subDays(5)->subHours(2),
            ],
            [
                $daniel,
                'Daniel Brooks changed lead "Internal CRM implementation" from New to Contacted',
                now()->subDays(4)->subHours(5),
            ],
            [
                $olivia,
                'Olivia Parker created lead "Analytics dashboard"',
                now()->subDays(4),
            ],
            [
                $sarah,
                'Sarah Mitchell changed lead "Website redesign platform" from Contacted to Qualified',
                now()->subDays(3)->subHours(4),
            ],
            [
                $manager,
                'Emma Collins created deal "Northstar development retainer"',
                now()->subDays(3),
            ],
            [
                $olivia,
                'Olivia Parker changed deal "Luma operations dashboard" from Open to Won',
                now()->subDays(2)->subHours(7),
            ],
            [
                $daniel,
                'Daniel Brooks completed task "Review Oakline integration requirements"',
                now()->subDays(2),
            ],
            [
                $sarah,
                'Sarah Mitchell created task "Send Northstar final proposal"',
                now()->subDay()->subHours(6),
            ],
            [
                $olivia,
                'Olivia Parker created task "Prepare Bluewave dashboard mockup"',
                now()->subDay()->subHours(3),
            ],
            [
                $manager,
                'Emma Collins updated customer "Vertex Systems"',
                now()->subHours(18),
            ],
            [
                $daniel,
                'Daniel Brooks created deal "PixelForge reporting system"',
                now()->subHours(12),
            ],
            [
                $sarah,
                'Sarah Mitchell changed lead "Fleet tracking improvements" from Contacted to Qualified',
                now()->subHours(8),
            ],
            [
                $olivia,
                'Olivia Parker completed task "Archive Luma kickoff notes"',
                now()->subHours(5),
            ],
            [
                $manager,
                'Emma Collins updated customer "BrightPeak Studio"',
                now()->subHours(2),
            ],
        ];

        foreach ($activities as [$user, $description, $createdAt]) {
            Activity::create([
                'user_id' => $user->id,
                'description' => $description,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);
        }
    }
}
