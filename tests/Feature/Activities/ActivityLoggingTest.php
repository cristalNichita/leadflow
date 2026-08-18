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

uses(RefreshDatabase::class);

test('creating a customer records activity', function () {
    $manager = User::factory()->create([
        'name' => 'Nikita',
        'role' => UserRole::Manager,
    ]);

    $this
        ->actingAs($manager)
        ->post(route('customers.store'), [
            'name' => 'Acme Ltd',
            'status' => CustomerStatus::Active->value,
        ])
        ->assertRedirect();

    expect(Activity::query()->count())
        ->toBe(1);

    $this->assertDatabaseHas('activities', [
        'user_id' => $manager->id,
        'description' => 'Nikita created customer "Acme Ltd"',
    ]);
});

test('changing lead status records activity', function () {
    $manager = User::factory()->create([
        'name' => 'Nikita',
        'role' => UserRole::Manager,
    ]);

    $customer = Customer::create([
        'name' => 'Acme Ltd',
        'status' => CustomerStatus::Active,
    ]);

    $lead = Lead::create([
        'title' => 'Website Redesign',
        'customer_id' => $customer->id,
        'estimated_value' => 5000,
        'status' => LeadStatus::New,
    ]);

    $this
        ->actingAs($manager)
        ->put(route('leads.update', $lead), [
            'title' => $lead->title,
            'customer_id' => $customer->id,
            'assigned_user_id' => null,
            'estimated_value' => 5000,
            'source' => null,
            'status' => LeadStatus::Qualified->value,
            'notes' => null,
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('activities', [
        'user_id' => $manager->id,
        'description' => 'Nikita changed lead "Website Redesign" from New to Qualified',
    ]);
});

test('changing deal status records activity', function () {
    $manager = User::factory()->create([
        'name' => 'Nikita',
        'role' => UserRole::Manager,
    ]);

    $customer = Customer::create([
        'name' => 'Acme Ltd',
        'status' => CustomerStatus::Active,
    ]);

    $deal = Deal::create([
        'title' => 'Annual Contract',
        'customer_id' => $customer->id,
        'value' => 12000,
        'status' => DealStatus::Open,
    ]);

    $this
        ->actingAs($manager)
        ->put(route('deals.update', $deal), [
            'title' => $deal->title,
            'customer_id' => $customer->id,
            'assigned_user_id' => null,
            'value' => 12000,
            'status' => DealStatus::Won->value,
            'expected_close_date' => null,
            'notes' => null,
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('activities', [
        'user_id' => $manager->id,
        'description' => 'Nikita changed deal "Annual Contract" from Open to Won',
    ]);
});

test('completing and reopening task records activity', function () {
    $user = User::factory()->create([
        'name' => 'Nikita',
        'role' => UserRole::User,
    ]);

    $customer = Customer::create([
        'name' => 'Acme Ltd',
        'status' => CustomerStatus::Active,
    ]);

    $task = Task::create([
        'title' => 'Call client',
        'assigned_user_id' => $user->id,
        'customer_id' => $customer->id,
        'priority' => TaskPriority::High,
        'completed' => false,
    ]);

    $this
        ->actingAs($user)
        ->patch(
            route('tasks.completion', $task),
            ['completed' => true],
        )
        ->assertRedirect();

    $this->assertDatabaseHas('activities', [
        'description' => 'Nikita completed task "Call client"',
    ]);

    $this
        ->actingAs($user)
        ->patch(
            route('tasks.completion', $task),
            ['completed' => false],
        )
        ->assertRedirect();

    $this->assertDatabaseHas('activities', [
        'description' => 'Nikita reopened task "Call client"',
    ]);
});

test('failed validation does not record activity', function () {
    $manager = User::factory()->create([
        'role' => UserRole::Manager,
    ]);

    $this
        ->actingAs($manager)
        ->post(route('customers.store'), [
            'name' => '',
            'status' => 'invalid',
        ])
        ->assertSessionHasErrors();

    expect(Activity::query()->count())
        ->toBe(0);
});
