<?php

namespace App\Data\Tasks;

use App\Enums\TaskPriority;

final readonly class TaskFiltersData
{
    public function __construct(
        public ?string $search = null,
        public ?TaskPriority $priority = null,
        public ?bool $completed = null,
        public ?int $assignedUserId = null,
        public int $perPage = 10,
    ) {}

    /**
     * @return array{
     *     search: string,
     *     priority: string,
     *     completed: bool|null,
     *     assigned_user_id: int|null
     * }
     */
    public function toArray(): array
    {
        return [
            'search' => $this->search ?? '',
            'priority' => $this->priority === null
                ? ''
                : $this->priority->value,
            'completed' => $this->completed,
            'assigned_user_id' => $this->assignedUserId,
        ];
    }
}
