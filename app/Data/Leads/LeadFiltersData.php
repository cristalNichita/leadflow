<?php

namespace App\Data\Leads;

use App\Enums\LeadStatus;

final readonly class LeadFiltersData
{
    public function __construct(
        public ?string $search = null,
        public ?LeadStatus $status = null,
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
