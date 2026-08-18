<?php

namespace App\Http\Resources;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Task
 */
class TaskResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,

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

            'customer_id' => $this->customer_id,

            'customer' => $this->whenLoaded(
                'customer',
                fn (): ?array => $this->customer === null
                    ? null
                    : [
                        'id' => $this->customer->id,
                        'name' => $this->customer->name,
                        'company' => $this->customer->company,
                    ],
            ),

            'deal_id' => $this->deal_id,

            'deal' => $this->whenLoaded(
                'deal',
                fn (): ?array => $this->deal === null
                    ? null
                    : [
                        'id' => $this->deal->id,
                        'title' => $this->deal->title,
                    ],
            ),

            'priority' => $this->priority->value,

            'due_date' => $this->due_date?->format(
                'Y-m-d',
            ),

            'completed' => $this->completed,

            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}
