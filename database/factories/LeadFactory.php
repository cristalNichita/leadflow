<?php

namespace Database\Factories;

use App\Enums\LeadStatus;
use App\Models\Customer;
use App\Models\Lead;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Lead>
 */
class LeadFactory extends Factory
{
    protected $model = Lead::class;

    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'customer_id' => Customer::factory(),
            'assigned_user_id' => null,
            'estimated_value' => fake()->randomFloat(
                2,
                1000,
                50000,
            ),
            'source' => fake()->randomElement([
                'Website',
                'Referral',
                'LinkedIn',
                'Email',
                'Conference',
            ]),
            'status' => fake()->randomElement([
                LeadStatus::New,
                LeadStatus::Contacted,
                LeadStatus::Qualified,
                LeadStatus::Won,
                LeadStatus::Lost,
            ]),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
