<?php

use App\Enums\CustomerStatus;
use App\Enums\DealStatus;
use App\Enums\UserRole;
use App\Models\Customer;
use App\Models\Deal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;

uses(RefreshDatabase::class);

function createDealCustomer(
    string $name = 'Acme Corporation',
): Customer {
    return Customer::create([
        'name' => $name,
        'company' => $name,
        'status' => CustomerStatus::Active,
    ]);
}

test('guest cannot access deals', function () {
    $this
        ->get(route('deals.index'))
        ->assertRedirect(route('login'));
});

test('manager can view deals index', function () {
    $manager = User::factory()->create([
        'role' => UserRole::Manager,
    ]);

    $customer = createDealCustomer();

    Deal::create([
        'title' => 'Annual Support Contract',
        'customer_id' => $customer->id,
        'assigned_user_id' => $manager->id,
        'value' => 18000,
        'status' => DealStatus::Open,
        'expected_close_date' => '2026-09-30',
    ]);

    $this
        ->actingAs($manager)
        ->get(route('deals.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('deals/index')
            ->has('deals.data', 1)
            ->where(
                'deals.data.0.title',
                'Annual Support Contract',
            )
            ->where(
                'deals.data.0.status',
                'open',
            )
            ->where(
                'deals.data.0.customer.name',
                'Acme Corporation',
            )
        );
});

test('manager can create a deal', function () {
    $manager = User::factory()->create([
        'role' => UserRole::Manager,
    ]);

    $assignee = User::factory()->create([
        'role' => UserRole::User,
    ]);

    $customer = createDealCustomer(
        'Northstar Digital',
    );

    $response = $this
        ->actingAs($manager)
        ->post(route('deals.store'), [
            'title' => 'Enterprise CRM Contract',
            'customer_id' => $customer->id,
            'assigned_user_id' => $assignee->id,
            'value' => 25000,
            'status' => DealStatus::Open->value,
            'expected_close_date' => '2026-10-15',
            'notes' => 'Proposal delivered to client.',
        ]);

    $deal = Deal::query()
        ->where('title', 'Enterprise CRM Contract')
        ->firstOrFail();

    $response
        ->assertRedirect(
            route('deals.show', $deal),
        )
        ->assertSessionHas(
            'success',
            'Deal created successfully.',
        );

    $this->assertDatabaseHas('deals', [
        'id' => $deal->id,
        'customer_id' => $customer->id,
        'assigned_user_id' => $assignee->id,
        'status' => DealStatus::Open->value,
    ]);

    $deal->refresh();

    expect($deal->expected_close_date?->toDateString())
        ->toBe('2026-10-15');
});

test('deal validation works', function () {
    $manager = User::factory()->create([
        'role' => UserRole::Manager,
    ]);

    $this
        ->actingAs($manager)
        ->post(route('deals.store'), [
            'title' => '',
            'customer_id' => 999999,
            'assigned_user_id' => 999999,
            'value' => -500,
            'status' => 'invalid-status',
            'expected_close_date' => 'not-a-date',
        ])
        ->assertSessionHasErrors([
            'title',
            'customer_id',
            'assigned_user_id',
            'value',
            'status',
            'expected_close_date',
        ]);

    $this->assertDatabaseCount(
        'deals',
        0,
    );
});

test('manager can update all deal fields', function () {
    $manager = User::factory()->create([
        'role' => UserRole::Manager,
    ]);

    $firstCustomer = createDealCustomer(
        'First Customer',
    );

    $secondCustomer = createDealCustomer(
        'Second Customer',
    );

    $assignee = User::factory()->create([
        'role' => UserRole::User,
    ]);

    $deal = Deal::create([
        'title' => 'Old Contract',
        'customer_id' => $firstCustomer->id,
        'assigned_user_id' => null,
        'value' => 5000,
        'status' => DealStatus::Open,
        'expected_close_date' => '2026-09-01',
        'notes' => null,
    ]);

    $this
        ->actingAs($manager)
        ->put(
            route('deals.update', $deal),
            [
                'title' => 'Updated Contract',
                'customer_id' => $secondCustomer->id,
                'assigned_user_id' => $assignee->id,
                'value' => 14500,
                'status' => DealStatus::Won->value,
                'expected_close_date' => '2026-10-20',
                'notes' => 'Contract signed.',
            ],
        )
        ->assertRedirect(
            route('deals.show', $deal),
        )
        ->assertSessionHas(
            'success',
            'Deal updated successfully.',
        );

    $this->assertDatabaseHas('deals', [
        'id' => $deal->id,
        'title' => 'Updated Contract',
        'customer_id' => $secondCustomer->id,
        'assigned_user_id' => $assignee->id,
        'status' => DealStatus::Won->value,
        'notes' => 'Contract signed.',
    ]);

    $deal->refresh();

    expect($deal->expected_close_date?->toDateString())
        ->toBe('2026-10-20');
});

test('manager can delete a deal', function () {
    $manager = User::factory()->create([
        'role' => UserRole::Manager,
    ]);

    $customer = createDealCustomer();

    $deal = Deal::create([
        'title' => 'Delete This Deal',
        'customer_id' => $customer->id,
        'value' => 3000,
        'status' => DealStatus::Open,
    ]);

    $this
        ->actingAs($manager)
        ->delete(
            route('deals.destroy', $deal),
        )
        ->assertRedirect(
            route('deals.index'),
        )
        ->assertSessionHas(
            'success',
            'Deal deleted successfully.',
        );

    $this->assertDatabaseMissing('deals', [
        'id' => $deal->id,
    ]);
});

test('regular user can only see assigned deals', function () {
    $user = User::factory()->create([
        'role' => UserRole::User,
    ]);

    $otherUser = User::factory()->create([
        'role' => UserRole::User,
    ]);

    $customer = createDealCustomer();

    $assignedDeal = Deal::create([
        'title' => 'Assigned Contract',
        'customer_id' => $customer->id,
        'assigned_user_id' => $user->id,
        'value' => 7500,
        'status' => DealStatus::Open,
    ]);

    $hiddenDeal = Deal::create([
        'title' => 'Hidden Contract',
        'customer_id' => $customer->id,
        'assigned_user_id' => $otherUser->id,
        'value' => 9000,
        'status' => DealStatus::Open,
    ]);

    $this
        ->actingAs($user)
        ->get(route('deals.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->has('deals.data', 1)
            ->where(
                'deals.data.0.id',
                $assignedDeal->id,
            )
        );

    $this
        ->actingAs($user)
        ->get(route('deals.show', $assignedDeal))
        ->assertOk();

    $this
        ->actingAs($user)
        ->get(route('deals.show', $hiddenDeal))
        ->assertForbidden();
});

test('regular user cannot create or delete deals', function () {
    $user = User::factory()->create([
        'role' => UserRole::User,
    ]);

    $customer = createDealCustomer();

    $deal = Deal::create([
        'title' => 'Protected Deal',
        'customer_id' => $customer->id,
        'assigned_user_id' => $user->id,
        'value' => 5000,
        'status' => DealStatus::Open,
    ]);

    $this
        ->actingAs($user)
        ->get(route('deals.create'))
        ->assertForbidden();

    $this
        ->actingAs($user)
        ->delete(
            route('deals.destroy', $deal),
        )
        ->assertForbidden();

    $this->assertDatabaseHas('deals', [
        'id' => $deal->id,
    ]);
});

test('regular user can update status and notes of assigned deal', function () {
    $user = User::factory()->create([
        'role' => UserRole::User,
    ]);

    $customer = createDealCustomer();

    $deal = Deal::create([
        'title' => 'Support Contract',
        'customer_id' => $customer->id,
        'assigned_user_id' => $user->id,
        'value' => 12000,
        'status' => DealStatus::Open,
        'expected_close_date' => '2026-10-01',
        'notes' => 'Initial negotiation.',
    ]);

    $this
        ->actingAs($user)
        ->put(
            route('deals.update', $deal),
            [
                'title' => $deal->title,
                'customer_id' => $deal->customer_id,
                'assigned_user_id' => $deal->assigned_user_id,
                'value' => $deal->value,
                'status' => DealStatus::Won->value,
                'expected_close_date' => $deal
                    ->expected_close_date
                    ?->format('Y-m-d'),
                'notes' => 'Client signed the contract.',
            ],
        )
        ->assertRedirect(
            route('deals.show', $deal),
        );

    $deal->refresh();

    expect($deal->status)
        ->toBe(DealStatus::Won)
        ->and($deal->notes)
        ->toBe('Client signed the contract.');
});

test('regular user cannot tamper with protected deal fields', function () {
    $user = User::factory()->create([
        'role' => UserRole::User,
    ]);

    $otherUser = User::factory()->create([
        'role' => UserRole::User,
    ]);

    $originalCustomer = createDealCustomer(
        'Original Customer',
    );

    $otherCustomer = createDealCustomer(
        'Other Customer',
    );

    $deal = Deal::create([
        'title' => 'Original Contract',
        'customer_id' => $originalCustomer->id,
        'assigned_user_id' => $user->id,
        'value' => 8000,
        'status' => DealStatus::Open,
        'expected_close_date' => '2026-09-15',
        'notes' => 'Original notes.',
    ]);

    $this
        ->actingAs($user)
        ->put(
            route('deals.update', $deal),
            [
                'title' => 'Tampered Contract',
                'customer_id' => $otherCustomer->id,
                'assigned_user_id' => $otherUser->id,
                'value' => 999999,
                'status' => DealStatus::Won->value,
                'expected_close_date' => '2030-01-01',
                'notes' => 'Legitimate updated notes.',
            ],
        )
        ->assertRedirect(
            route('deals.show', $deal),
        );

    $deal->refresh();

    expect($deal->title)
        ->toBe('Original Contract')
        ->and($deal->customer_id)
        ->toBe($originalCustomer->id)
        ->and($deal->assigned_user_id)
        ->toBe($user->id)
        ->and($deal->value)
        ->toBe('8000.00')
        ->and($deal->expected_close_date?->toDateString())
        ->toBe('2026-09-15')
        ->and($deal->status)
        ->toBe(DealStatus::Won)
        ->and($deal->notes)
        ->toBe('Legitimate updated notes.');
});

test('deals can be searched and filtered', function () {
    $manager = User::factory()->create([
        'role' => UserRole::Manager,
    ]);

    $assignee = User::factory()->create([
        'name' => 'Olivia Stone',
        'role' => UserRole::User,
    ]);

    $otherAssignee = User::factory()->create([
        'role' => UserRole::User,
    ]);

    $northstar = createDealCustomer(
        'Northstar Digital',
    );

    $bluewave = createDealCustomer(
        'Bluewave Media',
    );

    Deal::create([
        'title' => 'Annual Platform Contract',
        'customer_id' => $northstar->id,
        'assigned_user_id' => $assignee->id,
        'value' => 20000,
        'status' => DealStatus::Won,
        'expected_close_date' => '2026-09-20',
    ]);

    Deal::create([
        'title' => 'Marketing Website Contract',
        'customer_id' => $bluewave->id,
        'assigned_user_id' => $otherAssignee->id,
        'value' => 6000,
        'status' => DealStatus::Open,
    ]);

    $this
        ->actingAs($manager)
        ->get(route('deals.index', [
            'search' => 'Northstar',
            'status' => DealStatus::Won->value,
            'assigned_user_id' => $assignee->id,
        ]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->has('deals.data', 1)
            ->where(
                'deals.data.0.title',
                'Annual Platform Contract',
            )
            ->where(
                'filters.search',
                'Northstar',
            )
            ->where(
                'filters.status',
                'won',
            )
            ->where(
                'filters.assigned_user_id',
                $assignee->id,
            )
        );
});
