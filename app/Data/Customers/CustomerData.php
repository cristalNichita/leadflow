<?php

namespace App\Data\Customers;

use App\Enums\CustomerStatus;

final readonly class CustomerData
{
    public function __construct(
        public string $name,
        public ?string $company,
        public ?string $email,
        public ?string $phone,
        public CustomerStatus $status,
        public ?string $notes,
    ) {}

    /**
     * @param array{
     *     name: string,
     *     company?: string|null,
     *     email?: string|null,
     *     phone?: string|null,
     *     status: string,
     *     notes?: string|null
     * } $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            company: $data['company'] ?? null,
            email: $data['email'] ?? null,
            phone: $data['phone'] ?? null,
            status: CustomerStatus::from($data['status']),
            notes: $data['notes'] ?? null,
        );
    }

    /**
     * @return array{
     *     name: string,
     *     company: string|null,
     *     email: string|null,
     *     phone: string|null,
     *     status: string,
     *     notes: string|null
     * }
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'company' => $this->company,
            'email' => $this->email,
            'phone' => $this->phone,
            'status' => $this->status->value,
            'notes' => $this->notes,
        ];
    }
}
