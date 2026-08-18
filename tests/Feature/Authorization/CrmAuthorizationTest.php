<?php

use App\Enums\UserRole;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admin can perform any crm action', function () {
    $admin = User::factory()->create([
        'role' => UserRole::Admin,
    ]);

    $customer = Customer::create([
        'name' => 'Acme Ltd',
    ]);

    expect($admin->can('create', Customer::class))->toBeTrue()
        ->and($admin->can('delete', $customer))->toBeTrue()
        ->and($admin->can('viewAny', User::class))->toBeTrue();
});

test('manager can manage crm resources but cannot manage users', function () {
    $manager = User::factory()->create([
        'role' => UserRole::Manager,
    ]);

    $customer = Customer::create([
        'name' => 'Acme Ltd',
    ]);

    expect($manager->can('create', Customer::class))->toBeTrue()
        ->and($manager->can('update', $customer))->toBeTrue()
        ->and($manager->can('delete', $customer))->toBeTrue()
        ->and($manager->can('viewAny', User::class))->toBeFalse();
});

test('regular user can only access assigned lead', function () {
    $user = User::factory()->create([
        'role' => UserRole::User,
    ]);

    $otherUser = User::factory()->create([
        'role' => UserRole::User,
    ]);

    $customer = Customer::create([
        'name' => 'Acme Ltd',
    ]);

    $assignedLead = Lead::create([
        'title' => 'Website Redesign',
        'customer_id' => $customer->id,
        'assigned_user_id' => $user->id,
        'estimated_value' => 5000,
    ]);

    $otherLead = Lead::create([
        'title' => 'Mobile Application',
        'customer_id' => $customer->id,
        'assigned_user_id' => $otherUser->id,
        'estimated_value' => 8000,
    ]);

    expect($user->can('view', $assignedLead))->toBeTrue()
        ->and($user->can('update', $assignedLead))->toBeTrue()
        ->and($user->can('delete', $assignedLead))->toBeFalse()
        ->and($user->can('view', $otherLead))->toBeFalse()
        ->and($user->can('create', Lead::class))->toBeFalse()
        ->and($user->can('view', $customer))->toBeTrue();
});
