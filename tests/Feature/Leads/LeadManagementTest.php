<?php

use App\Enums\CustomerStatus;
use App\Enums\LeadStatus;
use App\Enums\UserRole;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;

uses(RefreshDatabase::class);

function createLeadCustomer(
    string $name = 'Acme Corporation',
): Customer {
    return Customer::create([
        'name' => $name,
        'company' => $name,
        'status' => CustomerStatus::Active,
    ]);
}

test('guest cannot access leads', function () {
    $this
        ->get(route('leads.index'))
        ->assertRedirect(route('login'));
});

test('manager can view leads index', function () {
    $manager = User::factory()->create([
        'role' => UserRole::Manager,
    ]);

    $customer = createLeadCustomer();

    Lead::create([
        'title' => 'Website Redesign',
        'customer_id' => $customer->id,
        'assigned_user_id' => $manager->id,
        'estimated_value' => 7500,
        'source' => 'Referral',
        'status' => LeadStatus::Qualified,
    ]);

    $this
        ->actingAs($manager)
        ->get(route('leads.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('leads/index')
            ->has('leads.data', 1)
            ->where(
                'leads.data.0.title',
                'Website Redesign',
            )
            ->where(
                'leads.data.0.status',
                'qualified',
            )
            ->where(
                'leads.data.0.customer.name',
                'Acme Corporation',
            )
        );
});

test('manager can create a lead', function () {
    $manager = User::factory()->create([
        'role' => UserRole::Manager,
    ]);

    $assignee = User::factory()->create([
        'role' => UserRole::User,
    ]);

    $customer = createLeadCustomer(
        'Northstar Digital',
    );

    $response = $this
        ->actingAs($manager)
        ->post(route('leads.store'), [
            'title' => 'CRM Implementation',
            'customer_id' => $customer->id,
            'assigned_user_id' => $assignee->id,
            'estimated_value' => 12500,
            'source' => 'Website',
            'status' => LeadStatus::New->value,
            'notes' => 'Requested a discovery call.',
        ]);

    $lead = Lead::query()
        ->where('title', 'CRM Implementation')
        ->firstOrFail();

    $response
        ->assertRedirect(
            route('leads.show', $lead),
        )
        ->assertSessionHas(
            'success',
            'Lead created successfully.',
        );

    $this->assertDatabaseHas('leads', [
        'id' => $lead->id,
        'customer_id' => $customer->id,
        'assigned_user_id' => $assignee->id,
        'source' => 'Website',
        'status' => LeadStatus::New->value,
    ]);
});

test('lead validation works', function () {
    $manager = User::factory()->create([
        'role' => UserRole::Manager,
    ]);

    $this
        ->actingAs($manager)
        ->post(route('leads.store'), [
            'title' => '',
            'customer_id' => 999999,
            'assigned_user_id' => 999999,
            'estimated_value' => -1,
            'status' => 'invalid-status',
        ])
        ->assertSessionHasErrors([
            'title',
            'customer_id',
            'assigned_user_id',
            'estimated_value',
            'status',
        ]);

    $this->assertDatabaseCount(
        'leads',
        0,
    );
});

test('manager can update all lead fields', function () {
    $manager = User::factory()->create([
        'role' => UserRole::Manager,
    ]);

    $firstCustomer = createLeadCustomer(
        'First Customer',
    );

    $secondCustomer = createLeadCustomer(
        'Second Customer',
    );

    $assignee = User::factory()->create([
        'role' => UserRole::User,
    ]);

    $lead = Lead::create([
        'title' => 'Old Opportunity',
        'customer_id' => $firstCustomer->id,
        'assigned_user_id' => null,
        'estimated_value' => 3000,
        'source' => 'Cold Call',
        'status' => LeadStatus::New,
        'notes' => null,
    ]);

    $this
        ->actingAs($manager)
        ->put(
            route('leads.update', $lead),
            [
                'title' => 'Updated Opportunity',
                'customer_id' => $secondCustomer->id,
                'assigned_user_id' => $assignee->id,
                'estimated_value' => 9500,
                'source' => 'LinkedIn',
                'status' => LeadStatus::Qualified->value,
                'notes' => 'Qualified after discovery call.',
            ],
        )
        ->assertRedirect(
            route('leads.show', $lead),
        )
        ->assertSessionHas(
            'success',
            'Lead updated successfully.',
        );

    $this->assertDatabaseHas('leads', [
        'id' => $lead->id,
        'title' => 'Updated Opportunity',
        'customer_id' => $secondCustomer->id,
        'assigned_user_id' => $assignee->id,
        'source' => 'LinkedIn',
        'status' => LeadStatus::Qualified->value,
    ]);
});

test('manager can delete a lead', function () {
    $manager = User::factory()->create([
        'role' => UserRole::Manager,
    ]);

    $customer = createLeadCustomer();

    $lead = Lead::create([
        'title' => 'Delete This Lead',
        'customer_id' => $customer->id,
        'estimated_value' => 1000,
        'status' => LeadStatus::New,
    ]);

    $this
        ->actingAs($manager)
        ->delete(
            route('leads.destroy', $lead),
        )
        ->assertRedirect(
            route('leads.index'),
        )
        ->assertSessionHas(
            'success',
            'Lead deleted successfully.',
        );

    $this->assertDatabaseMissing('leads', [
        'id' => $lead->id,
    ]);
});

test('regular user can only see assigned leads', function () {
    $user = User::factory()->create([
        'role' => UserRole::User,
    ]);

    $otherUser = User::factory()->create([
        'role' => UserRole::User,
    ]);

    $customer = createLeadCustomer();

    $assignedLead = Lead::create([
        'title' => 'Assigned Opportunity',
        'customer_id' => $customer->id,
        'assigned_user_id' => $user->id,
        'estimated_value' => 5000,
        'status' => LeadStatus::Contacted,
    ]);

    $hiddenLead = Lead::create([
        'title' => 'Hidden Opportunity',
        'customer_id' => $customer->id,
        'assigned_user_id' => $otherUser->id,
        'estimated_value' => 6000,
        'status' => LeadStatus::New,
    ]);

    $this
        ->actingAs($user)
        ->get(route('leads.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->has('leads.data', 1)
            ->where(
                'leads.data.0.id',
                $assignedLead->id,
            )
        );

    $this
        ->actingAs($user)
        ->get(route('leads.show', $assignedLead))
        ->assertOk();

    $this
        ->actingAs($user)
        ->get(route('leads.show', $hiddenLead))
        ->assertForbidden();
});

test('regular user cannot create or delete leads', function () {
    $user = User::factory()->create([
        'role' => UserRole::User,
    ]);

    $customer = createLeadCustomer();

    $lead = Lead::create([
        'title' => 'Protected Lead',
        'customer_id' => $customer->id,
        'assigned_user_id' => $user->id,
        'estimated_value' => 5000,
        'status' => LeadStatus::New,
    ]);

    $this
        ->actingAs($user)
        ->get(route('leads.create'))
        ->assertForbidden();

    $this
        ->actingAs($user)
        ->delete(
            route('leads.destroy', $lead),
        )
        ->assertForbidden();

    $this->assertDatabaseHas('leads', [
        'id' => $lead->id,
    ]);
});

test('regular user can update status and notes of assigned lead', function () {
    $user = User::factory()->create([
        'role' => UserRole::User,
    ]);

    $customer = createLeadCustomer();

    $lead = Lead::create([
        'title' => 'Sales Opportunity',
        'customer_id' => $customer->id,
        'assigned_user_id' => $user->id,
        'estimated_value' => 8000,
        'source' => 'Referral',
        'status' => LeadStatus::New,
        'notes' => 'Initial notes.',
    ]);

    $this
        ->actingAs($user)
        ->put(
            route('leads.update', $lead),
            [
                'title' => $lead->title,
                'customer_id' => $lead->customer_id,
                'assigned_user_id' => $lead->assigned_user_id,
                'estimated_value' => $lead->estimated_value,
                'source' => $lead->source,
                'status' => LeadStatus::Contacted->value,
                'notes' => 'Client was contacted.',
            ],
        )
        ->assertRedirect(
            route('leads.show', $lead),
        );

    $this->assertDatabaseHas('leads', [
        'id' => $lead->id,
        'status' => LeadStatus::Contacted->value,
        'notes' => 'Client was contacted.',
    ]);
});

test('regular user cannot tamper with protected lead fields', function () {
    $user = User::factory()->create([
        'role' => UserRole::User,
    ]);

    $otherUser = User::factory()->create([
        'role' => UserRole::User,
    ]);

    $originalCustomer = createLeadCustomer(
        'Original Customer',
    );

    $otherCustomer = createLeadCustomer(
        'Other Customer',
    );

    $lead = Lead::create([
        'title' => 'Original Title',
        'customer_id' => $originalCustomer->id,
        'assigned_user_id' => $user->id,
        'estimated_value' => 5000,
        'source' => 'Referral',
        'status' => LeadStatus::New,
        'notes' => 'Original notes.',
    ]);

    $this
        ->actingAs($user)
        ->put(
            route('leads.update', $lead),
            [
                'title' => 'Hacked Title',
                'customer_id' => $otherCustomer->id,
                'assigned_user_id' => $otherUser->id,
                'estimated_value' => 999999,
                'source' => 'Tampered Source',
                'status' => LeadStatus::Qualified->value,
                'notes' => 'Legitimate updated notes.',
            ],
        )
        ->assertRedirect(
            route('leads.show', $lead),
        );

    $lead->refresh();

    expect($lead->title)
        ->toBe('Original Title')
        ->and($lead->customer_id)
        ->toBe($originalCustomer->id)
        ->and($lead->assigned_user_id)
        ->toBe($user->id)
        ->and($lead->estimated_value)
        ->toBe('5000.00')
        ->and($lead->source)
        ->toBe('Referral')
        ->and($lead->status)
        ->toBe(LeadStatus::Qualified)
        ->and($lead->notes)
        ->toBe('Legitimate updated notes.');
});

test('leads can be searched and filtered', function () {
    $manager = User::factory()->create([
        'role' => UserRole::Manager,
    ]);

    $assignee = User::factory()->create([
        'name' => 'Sarah Connor',
        'role' => UserRole::User,
    ]);

    $otherAssignee = User::factory()->create([
        'role' => UserRole::User,
    ]);

    $northstar = createLeadCustomer(
        'Northstar Digital',
    );

    $bluewave = createLeadCustomer(
        'Bluewave Media',
    );

    Lead::create([
        'title' => 'Website Redesign',
        'customer_id' => $northstar->id,
        'assigned_user_id' => $assignee->id,
        'estimated_value' => 9000,
        'source' => 'Referral',
        'status' => LeadStatus::Qualified,
    ]);

    Lead::create([
        'title' => 'Mobile Application',
        'customer_id' => $bluewave->id,
        'assigned_user_id' => $otherAssignee->id,
        'estimated_value' => 15000,
        'source' => 'Website',
        'status' => LeadStatus::New,
    ]);

    $this
        ->actingAs($manager)
        ->get(route('leads.index', [
            'search' => 'Northstar',
            'status' => LeadStatus::Qualified->value,
            'assigned_user_id' => $assignee->id,
        ]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->has('leads.data', 1)
            ->where(
                'leads.data.0.title',
                'Website Redesign',
            )
            ->where(
                'filters.search',
                'Northstar',
            )
            ->where(
                'filters.status',
                'qualified',
            )
            ->where(
                'filters.assigned_user_id',
                $assignee->id,
            )
        );
});
