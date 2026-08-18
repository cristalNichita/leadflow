<?php

use App\Enums\CustomerStatus;
use App\Enums\DealStatus;
use App\Enums\TaskPriority;
use App\Enums\UserRole;
use App\Models\Customer;
use App\Models\Deal;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;

uses(RefreshDatabase::class);

function createTaskCustomer(
    string $name = 'Acme Corporation',
): Customer {
    return Customer::create([
        'name' => $name,
        'company' => $name,
        'status' => CustomerStatus::Active,
    ]);
}

function createRelatedDeal(
    Customer $customer,
    string $title = 'Support Contract',
): Deal {
    return Deal::create([
        'title' => $title,
        'customer_id' => $customer->id,
        'value' => 12000,
        'status' => DealStatus::Open,
    ]);
}

test('guest cannot access tasks', function () {
    $this
        ->get(route('tasks.index'))
        ->assertRedirect(route('login'));
});

test('manager can view tasks index', function () {
    $manager = User::factory()->create([
        'role' => UserRole::Manager,
    ]);

    $customer = createTaskCustomer();

    Task::create([
        'title' => 'Call customer',
        'description' => 'Discuss project requirements.',
        'assigned_user_id' => $manager->id,
        'customer_id' => $customer->id,
        'priority' => TaskPriority::High,
        'due_date' => '2026-08-25',
        'completed' => false,
    ]);

    $this
        ->actingAs($manager)
        ->get(route('tasks.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('tasks/index')
            ->has('tasks.data', 1)
            ->where(
                'tasks.data.0.title',
                'Call customer',
            )
            ->where(
                'tasks.data.0.priority',
                'high',
            )
            ->where(
                'tasks.data.0.customer.name',
                'Acme Corporation',
            )
            ->where(
                'tasks.data.0.completed',
                false,
            )
        );
});

test('manager can create a customer related task', function () {
    $manager = User::factory()->create([
        'role' => UserRole::Manager,
    ]);

    $assignee = User::factory()->create([
        'role' => UserRole::User,
    ]);

    $customer = createTaskCustomer(
        'Northstar Digital',
    );

    $response = $this
        ->actingAs($manager)
        ->post(route('tasks.store'), [
            'title' => 'Prepare proposal',
            'description' => 'Prepare the final commercial proposal.',
            'assigned_user_id' => $assignee->id,
            'customer_id' => $customer->id,
            'priority' => TaskPriority::High->value,
            'due_date' => '2026-08-28',
            'completed' => false,
        ]);

    $task = Task::query()
        ->where('title', 'Prepare proposal')
        ->firstOrFail();

    $response
        ->assertRedirect(
            route('tasks.show', $task),
        )
        ->assertSessionHas(
            'success',
            'Task created successfully.',
        );

    $this->assertDatabaseHas('tasks', [
        'id' => $task->id,
        'assigned_user_id' => $assignee->id,
        'customer_id' => $customer->id,
        'deal_id' => null,
        'priority' => TaskPriority::High->value,
    ]);

    $task->refresh();

    expect($task->due_date?->toDateString())
        ->toBe('2026-08-28')
        ->and($task->completed)
        ->toBeFalse();
});

test('manager can create a deal related task', function () {
    $manager = User::factory()->create([
        'role' => UserRole::Manager,
    ]);

    $customer = createTaskCustomer();
    $deal = createRelatedDeal($customer);

    $this
        ->actingAs($manager)
        ->post(route('tasks.store'), [
            'title' => 'Review contract',
            'description' => 'Review final contract before signing.',
            'deal_id' => $deal->id,
            'priority' => TaskPriority::Medium->value,
            'due_date' => '2026-09-01',
            'completed' => false,
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('tasks', [
        'title' => 'Review contract',
        'customer_id' => null,
        'deal_id' => $deal->id,
        'priority' => TaskPriority::Medium->value,
    ]);
});

test('task requires exactly one relation', function () {
    $manager = User::factory()->create([
        'role' => UserRole::Manager,
    ]);

    $customer = createTaskCustomer();
    $deal = createRelatedDeal($customer);

    $this
        ->actingAs($manager)
        ->post(route('tasks.store'), [
            'title' => 'Missing relation',
            'priority' => TaskPriority::Medium->value,
        ])
        ->assertSessionHasErrors([
            'customer_id',
        ]);

    $this
        ->actingAs($manager)
        ->post(route('tasks.store'), [
            'title' => 'Double relation',
            'customer_id' => $customer->id,
            'deal_id' => $deal->id,
            'priority' => TaskPriority::Medium->value,
        ])
        ->assertSessionHasErrors([
            'deal_id',
        ]);

    $this->assertDatabaseCount('tasks', 0);
});

test('manager can update all task fields', function () {
    $manager = User::factory()->create([
        'role' => UserRole::Manager,
    ]);

    $assignee = User::factory()->create([
        'role' => UserRole::User,
    ]);

    $customer = createTaskCustomer();
    $deal = createRelatedDeal($customer);

    $task = Task::create([
        'title' => 'Initial task',
        'description' => 'Initial description.',
        'customer_id' => $customer->id,
        'priority' => TaskPriority::Low,
        'due_date' => '2026-08-25',
        'completed' => false,
    ]);

    $this
        ->actingAs($manager)
        ->put(
            route('tasks.update', $task),
            [
                'title' => 'Updated task',
                'description' => 'Updated description.',
                'assigned_user_id' => $assignee->id,
                'customer_id' => null,
                'deal_id' => $deal->id,
                'priority' => TaskPriority::High->value,
                'due_date' => '2026-09-10',
                'completed' => true,
            ],
        )
        ->assertRedirect(
            route('tasks.show', $task),
        )
        ->assertSessionHas(
            'success',
            'Task updated successfully.',
        );

    $task->refresh();

    expect($task->title)
        ->toBe('Updated task')
        ->and($task->description)
        ->toBe('Updated description.')
        ->and($task->assigned_user_id)
        ->toBe($assignee->id)
        ->and($task->customer_id)
        ->toBeNull()
        ->and($task->deal_id)
        ->toBe($deal->id)
        ->and($task->priority)
        ->toBe(TaskPriority::High)
        ->and($task->due_date?->toDateString())
        ->toBe('2026-09-10')
        ->and($task->completed)
        ->toBeTrue();
});

test('manager can delete a task', function () {
    $manager = User::factory()->create([
        'role' => UserRole::Manager,
    ]);

    $customer = createTaskCustomer();

    $task = Task::create([
        'title' => 'Delete this task',
        'customer_id' => $customer->id,
        'priority' => TaskPriority::Medium,
    ]);

    $this
        ->actingAs($manager)
        ->delete(
            route('tasks.destroy', $task),
        )
        ->assertRedirect(
            route('tasks.index'),
        )
        ->assertSessionHas(
            'success',
            'Task deleted successfully.',
        );

    $this->assertDatabaseMissing('tasks', [
        'id' => $task->id,
    ]);
});

test('regular user can only see assigned tasks', function () {
    $user = User::factory()->create([
        'role' => UserRole::User,
    ]);

    $otherUser = User::factory()->create([
        'role' => UserRole::User,
    ]);

    $customer = createTaskCustomer();

    $assignedTask = Task::create([
        'title' => 'Assigned task',
        'assigned_user_id' => $user->id,
        'customer_id' => $customer->id,
        'priority' => TaskPriority::Medium,
    ]);

    $hiddenTask = Task::create([
        'title' => 'Hidden task',
        'assigned_user_id' => $otherUser->id,
        'customer_id' => $customer->id,
        'priority' => TaskPriority::Medium,
    ]);

    $this
        ->actingAs($user)
        ->get(route('tasks.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->has('tasks.data', 1)
            ->where(
                'tasks.data.0.id',
                $assignedTask->id,
            )
        );

    $this
        ->actingAs($user)
        ->get(route('tasks.show', $assignedTask))
        ->assertOk();

    $this
        ->actingAs($user)
        ->get(route('tasks.show', $hiddenTask))
        ->assertForbidden();
});

test('regular user cannot create or delete tasks', function () {
    $user = User::factory()->create([
        'role' => UserRole::User,
    ]);

    $customer = createTaskCustomer();

    $task = Task::create([
        'title' => 'Protected task',
        'assigned_user_id' => $user->id,
        'customer_id' => $customer->id,
        'priority' => TaskPriority::Medium,
    ]);

    $this
        ->actingAs($user)
        ->get(route('tasks.create'))
        ->assertForbidden();

    $this
        ->actingAs($user)
        ->delete(
            route('tasks.destroy', $task),
        )
        ->assertForbidden();

    $this->assertDatabaseHas('tasks', [
        'id' => $task->id,
    ]);
});

test('regular user can update description and completion state', function () {
    $user = User::factory()->create([
        'role' => UserRole::User,
    ]);

    $customer = createTaskCustomer();

    $task = Task::create([
        'title' => 'Call client',
        'description' => 'Original description.',
        'assigned_user_id' => $user->id,
        'customer_id' => $customer->id,
        'priority' => TaskPriority::High,
        'due_date' => '2026-08-30',
        'completed' => false,
    ]);

    $this
        ->actingAs($user)
        ->put(
            route('tasks.update', $task),
            [
                'title' => $task->title,
                'description' => 'Client was contacted successfully.',
                'assigned_user_id' => $task->assigned_user_id,
                'customer_id' => $task->customer_id,
                'deal_id' => null,
                'priority' => $task->priority->value,
                'due_date' => $task->due_date?->format('Y-m-d'),
                'completed' => true,
            ],
        )
        ->assertRedirect(
            route('tasks.show', $task),
        );

    $task->refresh();

    expect($task->description)
        ->toBe('Client was contacted successfully.')
        ->and($task->completed)
        ->toBeTrue();
});

test('regular user cannot tamper with protected task fields', function () {
    $user = User::factory()->create([
        'role' => UserRole::User,
    ]);

    $otherUser = User::factory()->create([
        'role' => UserRole::User,
    ]);

    $customer = createTaskCustomer(
        'Original Customer',
    );

    $otherCustomer = createTaskCustomer(
        'Other Customer',
    );

    $deal = createRelatedDeal(
        $otherCustomer,
        'Other Deal',
    );

    $task = Task::create([
        'title' => 'Original task',
        'description' => 'Original description.',
        'assigned_user_id' => $user->id,
        'customer_id' => $customer->id,
        'priority' => TaskPriority::Low,
        'due_date' => '2026-08-28',
        'completed' => false,
    ]);

    $this
        ->actingAs($user)
        ->put(
            route('tasks.update', $task),
            [
                'title' => 'Tampered title',
                'description' => 'Legitimate new description.',
                'assigned_user_id' => $otherUser->id,
                'customer_id' => null,
                'deal_id' => $deal->id,
                'priority' => TaskPriority::High->value,
                'due_date' => '2030-01-01',
                'completed' => true,
            ],
        )
        ->assertRedirect(
            route('tasks.show', $task),
        );

    $task->refresh();

    expect($task->title)
        ->toBe('Original task')
        ->and($task->description)
        ->toBe('Legitimate new description.')
        ->and($task->assigned_user_id)
        ->toBe($user->id)
        ->and($task->customer_id)
        ->toBe($customer->id)
        ->and($task->deal_id)
        ->toBeNull()
        ->and($task->priority)
        ->toBe(TaskPriority::Low)
        ->and($task->due_date?->toDateString())
        ->toBe('2026-08-28')
        ->and($task->completed)
        ->toBeTrue();
});

test('assigned user can complete and reopen task', function () {
    $user = User::factory()->create([
        'role' => UserRole::User,
    ]);

    $customer = createTaskCustomer();

    $task = Task::create([
        'title' => 'Completion task',
        'assigned_user_id' => $user->id,
        'customer_id' => $customer->id,
        'priority' => TaskPriority::Medium,
        'completed' => false,
    ]);

    $this
        ->actingAs($user)
        ->from(route('tasks.index'))
        ->patch(
            route('tasks.completion', $task),
            [
                'completed' => true,
            ],
        )
        ->assertRedirect(
            route('tasks.index'),
        )
        ->assertSessionHas(
            'success',
            'Task completed successfully.',
        );

    expect($task->refresh()->completed)
        ->toBeTrue();

    $this
        ->actingAs($user)
        ->from(route('tasks.index'))
        ->patch(
            route('tasks.completion', $task),
            [
                'completed' => false,
            ],
        )
        ->assertRedirect(
            route('tasks.index'),
        )
        ->assertSessionHas(
            'success',
            'Task reopened successfully.',
        );

    expect($task->refresh()->completed)
        ->toBeFalse();
});

test('tasks can be searched and filtered', function () {
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

    $customer = createTaskCustomer();

    Task::create([
        'title' => 'Prepare Northstar proposal',
        'description' => 'Finish commercial proposal.',
        'assigned_user_id' => $assignee->id,
        'customer_id' => $customer->id,
        'priority' => TaskPriority::High,
        'completed' => false,
    ]);

    Task::create([
        'title' => 'Archive old contract',
        'assigned_user_id' => $otherAssignee->id,
        'customer_id' => $customer->id,
        'priority' => TaskPriority::Low,
        'completed' => true,
    ]);

    $this
        ->actingAs($manager)
        ->get(route('tasks.index', [
            'search' => 'Northstar',
            'priority' => TaskPriority::High->value,
            'completed' => '0',
            'assigned_user_id' => $assignee->id,
        ]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->has('tasks.data', 1)
            ->where(
                'tasks.data.0.title',
                'Prepare Northstar proposal',
            )
            ->where(
                'filters.search',
                'Northstar',
            )
            ->where(
                'filters.priority',
                'high',
            )
            ->where(
                'filters.completed',
                false,
            )
            ->where(
                'filters.assigned_user_id',
                $assignee->id,
            )
        );
});
