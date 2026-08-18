<?php

namespace App\Services;

use App\Data\Tasks\TaskData;
use App\Data\Tasks\TaskFiltersData;
use App\Models\Task;
use App\Models\User;
use App\Repositories\Contracts\TaskRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final readonly class TaskService
{
    public function __construct(
        private TaskRepositoryInterface $tasks,
        private ActivityService $activities,
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

    public function create(
        User $user,
        TaskData $data,
    ): Task {
        return DB::transaction(function () use (
            $user,
            $data,
        ): Task {
            $task = $this->tasks->create(
                $data,
            );

            $this->activities->taskCreated(
                $user,
                $task,
            );

            return $task;
        });
    }

    public function update(
        User $user,
        Task $task,
        TaskData $data,
    ): Task {
        $wasCompleted = $task->completed;

        if (! $user->isAdmin() && ! $user->isManager()) {
            $data = new TaskData(
                title: $task->title,
                description: $data->description,
                assignedUserId: $task->assigned_user_id,
                customerId: $task->customer_id,
                dealId: $task->deal_id,
                priority: $task->priority,
                dueDate: $task->due_date?->format(
                    'Y-m-d',
                ),
                completed: $data->completed,
            );
        }

        return DB::transaction(function () use (
            $user,
            $task,
            $data,
            $wasCompleted,
        ): Task {
            $task = $this->tasks->update(
                $task,
                $data,
            );

            if ($wasCompleted !== $task->completed) {
                if ($task->completed) {
                    $this->activities->taskCompleted(
                        $user,
                        $task,
                    );
                } else {
                    $this->activities->taskReopened(
                        $user,
                        $task,
                    );
                }
            }

            return $task;
        });
    }

    public function delete(
        User $user,
        Task $task,
    ): void {
        DB::transaction(function () use (
            $user,
            $task,
        ): void {
            $title = $task->title;

            $this->tasks->delete(
                $task,
            );

            $this->activities->taskDeleted(
                $user,
                $title,
            );
        });
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

    public function setCompleted(
        User $user,
        Task $task,
        bool $completed,
    ): Task {
        if ($task->completed === $completed) {
            return $task;
        }

        return DB::transaction(function () use (
            $user,
            $task,
            $completed,
        ): Task {
            $task = $this->tasks->setCompleted(
                $task,
                $completed,
            );

            if ($task->completed) {
                $this->activities->taskCompleted(
                    $user,
                    $task,
                );
            } else {
                $this->activities->taskReopened(
                    $user,
                    $task,
                );
            }

            return $task;
        });
    }
}
