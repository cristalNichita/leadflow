<?php

namespace App\Data\Tasks;

use App\Enums\TaskPriority;

final readonly class TaskData
{
    public function __construct(
        public string $title,
        public ?string $description,
        public ?int $assignedUserId,
        public ?int $customerId,
        public ?int $dealId,
        public TaskPriority $priority,
        public ?string $dueDate,
        public bool $completed,
    ) {}

    /**
     * @param  array{
     *     title: string,
     *     description?: string|null,
     *     assigned_user_id?: int|null,
     *     customer_id?: int|null,
     *     deal_id?: int|null,
     *     priority: string,
     *     due_date?: string|null,
     *     completed?: bool|int|string
     * }  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            title: $data['title'],
            description: $data['description'] ?? null,
            assignedUserId: $data['assigned_user_id'] ?? null,
            customerId: $data['customer_id'] ?? null,
            dealId: $data['deal_id'] ?? null,
            priority: TaskPriority::from($data['priority']),
            dueDate: $data['due_date'] ?? null,
            completed: filter_var(
                $data['completed'] ?? false,
                FILTER_VALIDATE_BOOL,
            ),
        );
    }

    /**
     * @return array{
     *     title: string,
     *     description: string|null,
     *     assigned_user_id: int|null,
     *     customer_id: int|null,
     *     deal_id: int|null,
     *     priority: string,
     *     due_date: string|null,
     *     completed: bool
     * }
     */
    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'assigned_user_id' => $this->assignedUserId,
            'customer_id' => $this->customerId,
            'deal_id' => $this->dealId,
            'priority' => $this->priority->value,
            'due_date' => $this->dueDate,
            'completed' => $this->completed,
        ];
    }
}
