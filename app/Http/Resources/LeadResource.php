<?php

namespace App\Http\Resources;

use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Lead
 */
class LeadResource extends JsonResource
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

            'estimated_value' => $this->estimated_value,
            'source' => $this->source,
            'status' => $this->status->value,
            'notes' => $this->notes,

            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}
