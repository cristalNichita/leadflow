<?php

namespace App\Repositories\Contracts;

use App\Data\Dashboard\DashboardMetricsData;
use App\Models\Activity;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface DashboardRepositoryInterface
{
    public function metrics(User $user): DashboardMetricsData;

    /**
     * @return array{
     *     new: int,
     *     contacted: int,
     *     qualified: int,
     *     won: int,
     *     lost: int
     * }
     */
    public function leadStatusBreakdown(User $user): array;

    /**
     * @return array{
     *     open: int,
     *     won: int,
     *     lost: int
     * }
     */
    public function dealStatusBreakdown(User $user): array;

    /**
     * @return Collection<int, Activity>
     */
    public function recentActivities(
        User $user,
        int $limit = 8,
    ): Collection;

    /**
     * @return Collection<int, Task>
     */
    public function upcomingTasks(
        User $user,
        int $limit = 6,
    ): Collection;
}
