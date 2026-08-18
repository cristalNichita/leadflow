<?php

namespace Database\Factories;

use App\Enums\TaskPriority;
use App\Models\Customer;
use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'description' => fake()->sentence(),
            'assigned_user_id' => null,
            'customer_id' => Customer::factory(),
            'deal_id' => null,
            'priority' => fake()->randomElement([
                TaskPriority::Low,
                TaskPriority::Medium,
                TaskPriority::High,
            ]),
            'due_date' => fake()->dateTimeBetween(
                '-1 week',
                '+1 month',
            ),
            'completed' => fake()->boolean(25),
        ];
    }
}
