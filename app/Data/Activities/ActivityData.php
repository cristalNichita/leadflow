<?php

namespace App\Data\Activities;

final readonly class ActivityData
{
    public function __construct(
        public ?int $userId,
        public string $description,
    ) {}

    /**
     * @return array{
     *     user_id: int|null,
     *     description: string
     * }
     */
    public function toArray(): array
    {
        return [
            'user_id' => $this->userId,
            'description' => $this->description,
        ];
    }
}
