<?php

namespace App\Http\Resources;

use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Activity
 */
class ActivityResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'description' => $this->description,

            'user' => $this->whenLoaded(
                'user',
                fn (): ?array => $this->user === null
                    ? null
                    : [
                        'id' => $this->user->id,
                        'name' => $this->user->name,
                    ],
            ),

            'created_at' => $this->created_at->toISOString(),
        ];
    }
}
