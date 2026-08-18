<?php

namespace App\Repositories\Contracts;

use App\Data\Tasks\TaskData;
use App\Data\Tasks\TaskFiltersData;
use App\Models\Task;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface TaskRepositoryInterface
{
    /**
     * @return LengthAwarePaginator<int, Task>
     */
    public function paginateVisibleTo(
        User $user,
        TaskFiltersData $filters,
    ): LengthAwarePaginator;

    public function create(TaskData $data): Task;

    public function update(
        Task $task,
        TaskData $data,
    ): Task;

    public function delete(Task $task): void;

    public function loadDetails(Task $task): Task;

    /**
     * @return Collection<int, array{id: int, name: string}>
     */
    public function customerOptions(): Collection;

    /**
     * @return Collection<int, array{id: int, title: string}>
     */
    public function dealOptions(): Collection;

    /**
     * @return Collection<int, array{id: int, name: string}>
     */
    public function userOptions(): Collection;
}
