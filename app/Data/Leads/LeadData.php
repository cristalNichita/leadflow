<?php

namespace App\Data\Leads;

use App\Enums\LeadStatus;

final readonly class LeadData
{
    public function __construct(
        public string $title,
        public int $customerId,
        public ?int $assignedUserId,
        public float $estimatedValue,
        public ?string $source,
        public LeadStatus $status,
        public ?string $notes,
    ) {}

    /**
     * @param  array{
     *     title: string,
     *     customer_id: int,
     *     assigned_user_id?: int|null,
     *     estimated_value: numeric-string|int|float,
     *     source?: string|null,
     *     status: string,
     *     notes?: string|null
     * }  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            title: $data['title'],
            customerId: $data['customer_id'],
            assignedUserId: $data['assigned_user_id'] ?? null,
            estimatedValue: (float) $data['estimated_value'],
            source: $data['source'] ?? null,
            status: LeadStatus::from($data['status']),
            notes: $data['notes'] ?? null,
        );
    }

    /**
     * @return array{
     *     title: string,
     *     customer_id: int,
     *     assigned_user_id: int|null,
     *     estimated_value: float,
     *     source: string|null,
     *     status: string,
     *     notes: string|null
     * }
     */
    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'customer_id' => $this->customerId,
            'assigned_user_id' => $this->assignedUserId,
            'estimated_value' => $this->estimatedValue,
            'source' => $this->source,
            'status' => $this->status->value,
            'notes' => $this->notes,
        ];
    }
}
