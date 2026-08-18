<?php

namespace App\Services;

use App\Data\Tasks\TaskData;
use App\Data\Tasks\TaskFiltersData;
use App\Models\Task;
use App\Models\User;
use App\Repositories\Contracts\TaskRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

final readonly class TaskService
{
    public function __construct(
        private TaskRepositoryInterface $tasks,
    ) {}

    /**
     * @return LengthAwarePaginator<int, Task>
     */
    public function paginate(
        User $user,
        TaskFiltersData $filters,
    ): LengthAwarePaginator {
        return $this->tasks->paginateVisibleTo(
            $user,
            $filters,
        );
    }

    public function create(TaskData $data): Task
    {
        return $this->tasks->create($data);
    }

    public function update(
        User $user,
        Task $task,
        TaskData $data,
    ): Task {
        if (! $user->isAdmin() && ! $user->isManager()) {
            $data = new TaskData(
                title: $task->title,
                description: $data->description,
                assignedUserId: $task->assigned_user_id,
                customerId: $task->customer_id,
                dealId: $task->deal_id,
                priority: $task->priority,
                dueDate: $task->due_date?->format('Y-m-d'),
                completed: $data->completed,
            );
        }

        return $this->tasks->update(
            $task,
            $data,
        );
    }

    public function delete(Task $task): void
    {
        $this->tasks->delete($task);
    }

    public function details(Task $task): Task
    {
        return $this->tasks->loadDetails($task);
    }

    /**
     * @return array{
     *     customers: Collection<int, array{id: int, name: string}>,
     *     deals: Collection<int, array{id: int, title: string}>,
     *     users: Collection<int, array{id: int, name: string}>
     * }
     */
    public function formOptions(): array
    {
        return [
            'customers' => $this->tasks->customerOptions(),
            'deals' => $this->tasks->dealOptions(),
            'users' => $this->tasks->userOptions(),
        ];
    }

    /**
     * @return Collection<int, array{id: int, name: string}>
     */
    public function assigneeOptions(): Collection
    {
        return $this->tasks->userOptions();
    }
}
