<?php

namespace App\Data\Customers;

use App\Enums\CustomerStatus;

final readonly class CustomerFiltersData
{
    public function __construct(
        public ?string $search = null,
        public ?CustomerStatus $status = null,
        public int $perPage = 10,
    ) {}

    /**
     * @return array{
     *     search: string,
     *     status: string
     * }
     */
    public function toArray(): array
    {
        return [
            'search' => $this->search ?? '',
            'status' => $this->status === null
                ? ''
                : $this->status->value,
        ];
    }
}
