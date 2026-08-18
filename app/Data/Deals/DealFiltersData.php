<?php

namespace App\Data\Deals;

use App\Enums\DealStatus;

final readonly class DealFiltersData
{
    public function __construct(
        public ?string $search = null,
        public ?DealStatus $status = null,
        public ?int $assignedUserId = null,
        public int $perPage = 10,
    ) {}

    /**
     * @return array{
     *     search: string,
     *     status: string,
     *     assigned_user_id: int|null
     * }
     */
    public function toArray(): array
    {
        return [
            'search' => $this->search ?? '',
            'status' => $this->status === null
                ? ''
                : $this->status->value,
            'assigned_user_id' => $this->assignedUserId,
        ];
    }
}
