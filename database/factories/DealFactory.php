<?php

namespace Database\Factories;

use App\Enums\DealStatus;
use App\Models\Customer;
use App\Models\Deal;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Deal>
 */
class DealFactory extends Factory
{
    protected $model = Deal::class;

    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'customer_id' => Customer::factory(),
            'assigned_user_id' => null,
            'value' => fake()->randomFloat(
                2,
                3000,
                75000,
            ),
            'status' => fake()->randomElement([
                DealStatus::Open,
                DealStatus::Won,
                DealStatus::Lost,
            ]),
            'expected_close_date' => fake()
                ->optional()
                ->dateTimeBetween('now', '+3 months'),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
