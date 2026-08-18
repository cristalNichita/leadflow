<?php

namespace App\Services;

use App\Data\Dashboard\DashboardMetricsData;
use App\Models\Activity;
use App\Models\Task;
use App\Models\User;
use App\Repositories\Contracts\DashboardRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

final readonly class DashboardService
{
    public function __construct(
        private DashboardRepositoryInterface $dashboard,
    ) {}

    /**
     * @return array{
     *     metrics: DashboardMetricsData,
     *     lead_status: array{
     *         new: int,
     *         contacted: int,
     *         qualified: int,
     *         won: int,
     *         lost: int
     *     },
     *     deal_status: array{
     *         open: int,
     *         won: int,
     *         lost: int
     *     },
     *     recent_activities: Collection<int, Activity>,
     *     upcoming_tasks: Collection<int, Task>
     * }
     */
    public function overview(User $user): array
    {
        return [
            'metrics' => $this->dashboard->metrics(
                $user,
            ),

            'lead_status' => $this
                ->dashboard
                ->leadStatusBreakdown($user),

            'deal_status' => $this
                ->dashboard
                ->dealStatusBreakdown($user),

            'recent_activities' => $this
                ->dashboard
                ->recentActivities($user),

            'upcoming_tasks' => $this
                ->dashboard
                ->upcomingTasks($user),
        ];
    }
}
