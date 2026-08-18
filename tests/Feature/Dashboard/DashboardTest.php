<?php

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
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;

uses(RefreshDatabase::class);

function createDashboardCustomer(
    string $name = 'Acme Corporation',
): Customer {
    return Customer::create([
        'name' => $name,
        'company' => $name,
        'status' => CustomerStatus::Active,
    ]);
}

test('guest cannot access dashboard', function () {
    $this
        ->get(route('dashboard'))
        ->assertRedirect(route('login'));
});

test('manager sees workspace metrics', function () {
    $manager = User::factory()->create([
        'role' => UserRole::Manager,
    ]);

    $customerOne = createDashboardCustomer(
        'Northstar Digital',
    );

    $customerTwo = createDashboardCustomer(
        'Bluewave Media',
    );

    Lead::create([
        'title' => 'New Lead',
        'customer_id' => $customerOne->id,
        'estimated_value' => 5000,
        'status' => LeadStatus::New,
    ]);

    Lead::create([
        'title' => 'Qualified Lead',
        'customer_id' => $customerOne->id,
        'estimated_value' => 8000,
        'status' => LeadStatus::Qualified,
    ]);

    Lead::create([
        'title' => 'Won Lead',
        'customer_id' => $customerTwo->id,
        'estimated_value' => 10000,
        'status' => LeadStatus::Won,
    ]);

    Deal::create([
        'title' => 'Open Contract',
        'customer_id' => $customerOne->id,
        'value' => 12000,
        'status' => DealStatus::Open,
    ]);

    Deal::create([
        'title' => 'Won Contract',
        'customer_id' => $customerTwo->id,
        'value' => 25000,
        'status' => DealStatus::Won,
    ]);

    Deal::create([
        'title' => 'Second Won Contract',
        'customer_id' => $customerTwo->id,
        'value' => 7500,
        'status' => DealStatus::Won,
    ]);

    $this
        ->actingAs($manager)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('dashboard')
            ->where(
                'metrics.total_customers',
                2,
            )
            ->where(
                'metrics.active_leads',
                2,
            )
            ->where(
                'metrics.open_deals',
                1,
            )
            ->where(
                'metrics.won_revenue',
                32500,
            )
        );
});

test('dashboard returns lead status breakdown', function () {
    $manager = User::factory()->create([
        'role' => UserRole::Manager,
    ]);

    $customer = createDashboardCustomer();

    foreach ([
        LeadStatus::New,
        LeadStatus::New,
        LeadStatus::Contacted,
        LeadStatus::Qualified,
        LeadStatus::Qualified,
        LeadStatus::Qualified,
        LeadStatus::Won,
        LeadStatus::Lost,
    ] as $index => $status) {
        Lead::create([
            'title' => "Lead {$index}",
            'customer_id' => $customer->id,
            'estimated_value' => 1000,
            'status' => $status,
        ]);
    }

    $this
        ->actingAs($manager)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('leadStatus.new', 2)
            ->where('leadStatus.contacted', 1)
            ->where('leadStatus.qualified', 3)
            ->where('leadStatus.won', 1)
            ->where('leadStatus.lost', 1)
        );
});

test('dashboard returns deal status breakdown', function () {
    $manager = User::factory()->create([
        'role' => UserRole::Manager,
    ]);

    $customer = createDashboardCustomer();

    foreach ([
        DealStatus::Open,
        DealStatus::Open,
        DealStatus::Open,
        DealStatus::Won,
        DealStatus::Won,
        DealStatus::Lost,
    ] as $index => $status) {
        Deal::create([
            'title' => "Deal {$index}",
            'customer_id' => $customer->id,
            'value' => 5000,
            'status' => $status,
        ]);
    }

    $this
        ->actingAs($manager)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('dealStatus.open', 3)
            ->where('dealStatus.won', 2)
            ->where('dealStatus.lost', 1)
        );
});

test('dashboard shows recent activities in latest order', function () {
    $manager = User::factory()->create([
        'name' => 'Olivia Stone',
        'role' => UserRole::Manager,
    ]);

    Activity::create([
        'user_id' => $manager->id,
        'description' => 'First activity',
        'created_at' => now()->subMinutes(10),
        'updated_at' => now()->subMinutes(10),
    ]);

    Activity::create([
        'user_id' => $manager->id,
        'description' => 'Latest activity',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this
        ->actingAs($manager)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->has('recentActivities', 2)
            ->where(
                'recentActivities.0.description',
                'Latest activity',
            )
            ->where(
                'recentActivities.1.description',
                'First activity',
            )
            ->where(
                'recentActivities.0.user.name',
                'Olivia Stone',
            )
        );
});

test('dashboard shows closest open tasks first', function () {
    $manager = User::factory()->create([
        'role' => UserRole::Manager,
    ]);

    $customer = createDashboardCustomer();

    Task::create([
        'title' => 'Later task',
        'customer_id' => $customer->id,
        'priority' => TaskPriority::Low,
        'due_date' => '2026-09-20',
        'completed' => false,
    ]);

    Task::create([
        'title' => 'Closest task',
        'customer_id' => $customer->id,
        'priority' => TaskPriority::High,
        'due_date' => '2026-08-20',
        'completed' => false,
    ]);

    Task::create([
        'title' => 'Completed task',
        'customer_id' => $customer->id,
        'priority' => TaskPriority::Medium,
        'due_date' => '2026-08-19',
        'completed' => true,
    ]);

    $this
        ->actingAs($manager)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->has('upcomingTasks', 2)
            ->where(
                'upcomingTasks.0.title',
                'Closest task',
            )
            ->where(
                'upcomingTasks.1.title',
                'Later task',
            )
        );
});

test('dashboard limits recent activities and upcoming tasks', function () {
    $manager = User::factory()->create([
        'role' => UserRole::Manager,
    ]);

    $customer = createDashboardCustomer();

    foreach (range(1, 12) as $index) {
        Activity::create([
            'user_id' => $manager->id,
            'description' => "Activity {$index}",
        ]);
    }

    foreach (range(1, 9) as $index) {
        Task::create([
            'title' => "Task {$index}",
            'customer_id' => $customer->id,
            'priority' => TaskPriority::Medium,
            'due_date' => now()
                ->addDays($index)
                ->toDateString(),
            'completed' => false,
        ]);
    }

    $this
        ->actingAs($manager)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->has('recentActivities', 8)
            ->has('upcomingTasks', 6)
        );
});

test('regular user only sees assigned crm data on dashboard', function () {
    $user = User::factory()->create([
        'name' => 'Assigned User',
        'role' => UserRole::User,
    ]);

    $otherUser = User::factory()->create([
        'name' => 'Other User',
        'role' => UserRole::User,
    ]);

    $visibleCustomer = createDashboardCustomer(
        'Visible Customer',
    );

    $hiddenCustomer = createDashboardCustomer(
        'Hidden Customer',
    );

    Lead::create([
        'title' => 'My Lead',
        'customer_id' => $visibleCustomer->id,
        'assigned_user_id' => $user->id,
        'estimated_value' => 5000,
        'status' => LeadStatus::Qualified,
    ]);

    Lead::create([
        'title' => 'Other Lead',
        'customer_id' => $hiddenCustomer->id,
        'assigned_user_id' => $otherUser->id,
        'estimated_value' => 7000,
        'status' => LeadStatus::New,
    ]);

    Deal::create([
        'title' => 'My Won Deal',
        'customer_id' => $visibleCustomer->id,
        'assigned_user_id' => $user->id,
        'value' => 15000,
        'status' => DealStatus::Won,
    ]);

    Deal::create([
        'title' => 'Other Open Deal',
        'customer_id' => $hiddenCustomer->id,
        'assigned_user_id' => $otherUser->id,
        'value' => 50000,
        'status' => DealStatus::Open,
    ]);

    Task::create([
        'title' => 'My task',
        'assigned_user_id' => $user->id,
        'customer_id' => $visibleCustomer->id,
        'priority' => TaskPriority::High,
        'due_date' => '2026-08-25',
        'completed' => false,
    ]);

    Task::create([
        'title' => 'Other task',
        'assigned_user_id' => $otherUser->id,
        'customer_id' => $hiddenCustomer->id,
        'priority' => TaskPriority::High,
        'due_date' => '2026-08-24',
        'completed' => false,
    ]);

    Activity::create([
        'user_id' => $user->id,
        'description' => 'My activity',
    ]);

    Activity::create([
        'user_id' => $otherUser->id,
        'description' => 'Other activity',
    ]);

    $this
        ->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where(
                'metrics.total_customers',
                1,
            )
            ->where(
                'metrics.active_leads',
                1,
            )
            ->where(
                'metrics.open_deals',
                0,
            )
            ->where(
                'metrics.won_revenue',
                15000,
            )
            ->where(
                'leadStatus.qualified',
                1,
            )
            ->where(
                'leadStatus.new',
                0,
            )
            ->where(
                'dealStatus.won',
                1,
            )
            ->where(
                'dealStatus.open',
                0,
            )
            ->has(
                'upcomingTasks',
                1,
            )
            ->where(
                'upcomingTasks.0.title',
                'My task',
            )
            ->has(
                'recentActivities',
                1,
            )
            ->where(
                'recentActivities.0.description',
                'My activity',
            )
        );
});
