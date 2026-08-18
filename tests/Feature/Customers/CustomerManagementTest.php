<?php

use App\Enums\CustomerStatus;
use App\Enums\UserRole;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;

uses(RefreshDatabase::class);

test('guest cannot access customers', function () {
    $this
        ->get(route('customers.index'))
        ->assertRedirect(route('login'));
});

test('manager can view customers index', function () {
    $manager = User::factory()->create([
        'role' => UserRole::Manager,
    ]);

    Customer::create([
        'name' => 'Acme Corporation',
        'company' => 'Acme',
        'email' => 'hello@acme.test',
        'status' => CustomerStatus::Active,
    ]);

    $this
        ->actingAs($manager)
        ->get(route('customers.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('customers/index')
            ->has('customers.data', 1)
            ->where(
                'customers.data.0.name',
                'Acme Corporation',
            )
            ->where(
                'customers.data.0.status',
                'active',
            )
        );
});

test('manager can create a customer', function () {
    $manager = User::factory()->create([
        'role' => UserRole::Manager,
    ]);

    $response = $this
        ->actingAs($manager)
        ->post(route('customers.store'), [
            'name' => 'Northstar Digital',
            'company' => 'Northstar Ltd',
            'email' => 'hello@northstar.test',
            'phone' => '+373 69 555 421',
            'status' => CustomerStatus::Active->value,
            'notes' => 'Potential long-term client.',
        ]);

    $customer = Customer::query()
        ->where('email', 'hello@northstar.test')
        ->firstOrFail();

    $response
        ->assertRedirect(
            route('customers.show', $customer),
        )
        ->assertSessionHas(
            'success',
            'Customer created successfully.',
        );

    $this->assertDatabaseHas('customers', [
        'name' => 'Northstar Digital',
        'company' => 'Northstar Ltd',
        'email' => 'hello@northstar.test',
        'status' => CustomerStatus::Active->value,
    ]);
});

test('customer validation works', function () {
    $manager = User::factory()->create([
        'role' => UserRole::Manager,
    ]);

    $this
        ->actingAs($manager)
        ->post(route('customers.store'), [
            'name' => '',
            'email' => 'not-an-email',
            'status' => 'invalid-status',
        ])
        ->assertSessionHasErrors([
            'name',
            'email',
            'status',
        ]);

    $this->assertDatabaseCount(
        'customers',
        0,
    );
});

test('manager can update a customer', function () {
    $manager = User::factory()->create([
        'role' => UserRole::Manager,
    ]);

    $customer = Customer::create([
        'name' => 'Old Customer',
        'company' => 'Old Company',
        'status' => CustomerStatus::Active,
    ]);

    $this
        ->actingAs($manager)
        ->put(
            route('customers.update', $customer),
            [
                'name' => 'Updated Customer',
                'company' => 'Updated Company',
                'email' => 'updated@example.test',
                'phone' => null,
                'status' => CustomerStatus::Inactive->value,
                'notes' => 'Updated notes.',
            ],
        )
        ->assertRedirect(
            route('customers.show', $customer),
        )
        ->assertSessionHas(
            'success',
            'Customer updated successfully.',
        );

    $this->assertDatabaseHas('customers', [
        'id' => $customer->id,
        'name' => 'Updated Customer',
        'company' => 'Updated Company',
        'status' => CustomerStatus::Inactive->value,
    ]);
});

test('manager can delete a customer', function () {
    $manager = User::factory()->create([
        'role' => UserRole::Manager,
    ]);

    $customer = Customer::create([
        'name' => 'Delete Me',
        'status' => CustomerStatus::Active,
    ]);

    $this
        ->actingAs($manager)
        ->delete(
            route('customers.destroy', $customer),
        )
        ->assertRedirect(
            route('customers.index'),
        )
        ->assertSessionHas(
            'success',
            'Customer deleted successfully.',
        );

    $this->assertDatabaseMissing('customers', [
        'id' => $customer->id,
    ]);
});

test('regular user cannot create or delete customers', function () {
    $user = User::factory()->create([
        'role' => UserRole::User,
    ]);

    $customer = Customer::create([
        'name' => 'Protected Customer',
        'status' => CustomerStatus::Active,
    ]);

    $this
        ->actingAs($user)
        ->get(route('customers.create'))
        ->assertForbidden();

    $this
        ->actingAs($user)
        ->delete(
            route('customers.destroy', $customer),
        )
        ->assertForbidden();

    $this->assertDatabaseHas('customers', [
        'id' => $customer->id,
    ]);
});

test('regular user can only see customers related to assigned work', function () {
    $user = User::factory()->create([
        'role' => UserRole::User,
    ]);

    $visibleCustomer = Customer::create([
        'name' => 'Visible Customer',
        'status' => CustomerStatus::Active,
    ]);

    $hiddenCustomer = Customer::create([
        'name' => 'Hidden Customer',
        'status' => CustomerStatus::Active,
    ]);

    Lead::create([
        'title' => 'Assigned Lead',
        'customer_id' => $visibleCustomer->id,
        'assigned_user_id' => $user->id,
        'estimated_value' => 5000,
    ]);

    $this
        ->actingAs($user)
        ->get(route('customers.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->has('customers.data', 1)
            ->where(
                'customers.data.0.id',
                $visibleCustomer->id,
            )
        );

    $this
        ->actingAs($user)
        ->get(
            route(
                'customers.show',
                $visibleCustomer,
            ),
        )
        ->assertOk();

    $this
        ->actingAs($user)
        ->get(
            route(
                'customers.show',
                $hiddenCustomer,
            ),
        )
        ->assertForbidden();
});

test('customers can be searched and filtered by status', function () {
    $manager = User::factory()->create([
        'role' => UserRole::Manager,
    ]);

    Customer::create([
        'name' => 'Sophia Carter',
        'company' => 'BrightPeak Studio',
        'status' => CustomerStatus::Active,
    ]);

    Customer::create([
        'name' => 'Mia Thompson',
        'company' => 'Bluewave Media',
        'status' => CustomerStatus::Inactive,
    ]);

    $this
        ->actingAs($manager)
        ->get(route('customers.index', [
            'search' => 'brightpeak',
            'status' => CustomerStatus::Active->value,
        ]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->has('customers.data', 1)
            ->where(
                'customers.data.0.name',
                'Sophia Carter',
            )
            ->where(
                'filters.search',
                'brightpeak',
            )
            ->where(
                'filters.status',
                'active',
            )
        );
});
