<?php

namespace App\Http\Resources;

use App\Models\Deal;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Deal
 */
class DealResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,

            'customer_id' => $this->customer_id,

            'customer' => $this->whenLoaded(
                'customer',
                fn (): array => [
                    'id' => $this->customer->id,
                    'name' => $this->customer->name,
                    'company' => $this->customer->company,
                ],
            ),

            'assigned_user_id' => $this->assigned_user_id,

            'assigned_user' => $this->whenLoaded(
                'assignedUser',
                fn (): ?array => $this->assignedUser === null
                    ? null
                    : [
                        'id' => $this->assignedUser->id,
                        'name' => $this->assignedUser->name,
                        'email' => $this->assignedUser->email,
                    ],
            ),

            'value' => $this->value,
            'status' => $this->status->value,

            'expected_close_date' => $this->expected_close_date?->format(
                'Y-m-d',
            ),

            'notes' => $this->notes,

            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}
