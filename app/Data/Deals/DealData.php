<?php

namespace App\Data\Deals;

use App\Enums\DealStatus;

final readonly class DealData
{
    public function __construct(
        public string $title,
        public int $customerId,
        public ?int $assignedUserId,
        public float $value,
        public DealStatus $status,
        public ?string $expectedCloseDate,
        public ?string $notes,
    ) {}

    /**
     * @param  array{
     *     title: string,
     *     customer_id: int,
     *     assigned_user_id?: int|null,
     *     value: numeric-string|int|float,
     *     status: string,
     *     expected_close_date?: string|null,
     *     notes?: string|null
     * }  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            title: $data['title'],
            customerId: $data['customer_id'],
            assignedUserId: $data['assigned_user_id'] ?? null,
            value: (float) $data['value'],
            status: DealStatus::from($data['status']),
            expectedCloseDate: $data['expected_close_date'] ?? null,
            notes: $data['notes'] ?? null,
        );
    }

    /**
     * @return array{
     *     title: string,
     *     customer_id: int,
     *     assigned_user_id: int|null,
     *     value: float,
     *     status: string,
     *     expected_close_date: string|null,
     *     notes: string|null
     * }
     */
    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'customer_id' => $this->customerId,
            'assigned_user_id' => $this->assignedUserId,
            'value' => $this->value,
            'status' => $this->status->value,
            'expected_close_date' => $this->expectedCloseDate,
            'notes' => $this->notes,
        ];
    }
}
