<?php

namespace App\Repositories;

use App\Data\Tasks\TaskData;
use App\Data\Tasks\TaskFiltersData;
use App\Models\Customer;
use App\Models\Deal;
use App\Models\Task;
use App\Models\User;
use App\Repositories\Contracts\TaskRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

final class TaskRepository implements TaskRepositoryInterface
{
    /**
     * @return LengthAwarePaginator<int, Task>
     */
    public function paginateVisibleTo(
        User $user,
        TaskFiltersData $filters,
    ): LengthAwarePaginator {
        $query = Task::query()
            ->with([
                'assignedUser:id,name,email',
                'customer:id,name,company',
                'deal:id,title,customer_id',
            ]);

        if (! $user->isAdmin() && ! $user->isManager()) {
            $query->where(
                'assigned_user_id',
                $user->id,
            );
        }

        if ($filters->search !== null) {
            $search = '%'.$filters->search.'%';

            $query->where(function ($query) use ($search): void {
                $query
                    ->whereLike('title', $search)
                    ->orWhereLike('description', $search);
            });
        }

        if ($filters->priority !== null) {
            $query->where(
                'priority',
                $filters->priority->value,
            );
        }

        if ($filters->completed !== null) {
            $query->where(
                'completed',
                $filters->completed,
            );
        }

        if ($filters->assignedUserId !== null) {
            $query->where(
                'assigned_user_id',
                $filters->assignedUserId,
            );
        }

        return $query
            ->orderBy('completed')
            ->orderBy('due_date')
            ->latest('id')
            ->paginate($filters->perPage)
            ->withQueryString();
    }

    public function create(TaskData $data): Task
    {
        return Task::create(
            $data->toArray(),
        );
    }

    public function update(
        Task $task,
        TaskData $data,
    ): Task {
        $task->update(
            $data->toArray(),
        );

        return $task->refresh();
    }

    public function delete(Task $task): void
    {
        $task->delete();
    }

    public function loadDetails(Task $task): Task
    {
        return $task->load([
            'assignedUser:id,name,email',
            'customer:id,name,company,email,phone',
            'deal:id,title,customer_id,value,status',
        ]);
    }

    /**
     * @return Collection<int, array{id: int, name: string}>
     */
    public function customerOptions(): Collection
    {
        return Customer::query()
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(
                fn (Customer $customer): array => [
                    'id' => $customer->id,
                    'name' => $customer->name,
                ],
            );
    }

    /**
     * @return Collection<int, array{id: int, title: string}>
     */
    public function dealOptions(): Collection
    {
        return Deal::query()
            ->orderBy('title')
            ->get(['id', 'title'])
            ->map(
                fn (Deal $deal): array => [
                    'id' => $deal->id,
                    'title' => $deal->title,
                ],
            );
    }

    /**
     * @return Collection<int, array{id: int, name: string}>
     */
    public function userOptions(): Collection
    {
        return User::query()
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(
                fn (User $user): array => [
                    'id' => $user->id,
                    'name' => $user->name,
                ],
            );
    }

    public function setCompleted(
        Task $task,
        bool $completed,
    ): Task {
        $task->update([
            'completed' => $completed,
        ]);

        return $task->refresh();
    }
}
