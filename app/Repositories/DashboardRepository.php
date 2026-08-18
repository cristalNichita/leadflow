<?php

namespace App\Repositories;

use App\Data\Dashboard\DashboardMetricsData;
use App\Enums\DealStatus;
use App\Enums\LeadStatus;
use App\Models\Activity;
use App\Models\Customer;
use App\Models\Deal;
use App\Models\Lead;
use App\Models\Task;
use App\Models\User;
use App\Repositories\Contracts\DashboardRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

final class DashboardRepository implements DashboardRepositoryInterface
{
    public function metrics(User $user): DashboardMetricsData
    {
        $totalCustomers = $this
            ->visibleCustomersQuery($user)
            ->count();

        $activeLeads = $this
            ->visibleLeadsQuery($user)
            ->whereIn('status', [
                LeadStatus::New->value,
                LeadStatus::Contacted->value,
                LeadStatus::Qualified->value,
            ])
            ->count();

        $openDeals = $this
            ->visibleDealsQuery($user)
            ->where(
                'status',
                DealStatus::Open->value,
            )
            ->count();

        $wonRevenue = (float) $this
            ->visibleDealsQuery($user)
            ->where(
                'status',
                DealStatus::Won->value,
            )
            ->sum('value');

        return new DashboardMetricsData(
            totalCustomers: $totalCustomers,
            activeLeads: $activeLeads,
            openDeals: $openDeals,
            wonRevenue: $wonRevenue,
        );
    }

    /**
     * @return array{
     *     new: int,
     *     contacted: int,
     *     qualified: int,
     *     won: int,
     *     lost: int
     * }
     */
    public function leadStatusBreakdown(User $user): array
    {
        $counts = $this
            ->visibleLeadsQuery($user)
            ->select('status')
            ->selectRaw('COUNT(*) AS aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        return [
            'new' => (int) ($counts->get(
                LeadStatus::New->value,
            ) ?? 0),

            'contacted' => (int) ($counts->get(
                LeadStatus::Contacted->value,
            ) ?? 0),

            'qualified' => (int) ($counts->get(
                LeadStatus::Qualified->value,
            ) ?? 0),

            'won' => (int) ($counts->get(
                LeadStatus::Won->value,
            ) ?? 0),

            'lost' => (int) ($counts->get(
                LeadStatus::Lost->value,
            ) ?? 0),
        ];
    }

    /**
     * @return array{
     *     open: int,
     *     won: int,
     *     lost: int
     * }
     */
    public function dealStatusBreakdown(User $user): array
    {
        $counts = $this
            ->visibleDealsQuery($user)
            ->select('status')
            ->selectRaw('COUNT(*) AS aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        return [
            'open' => (int) ($counts->get(
                DealStatus::Open->value,
            ) ?? 0),

            'won' => (int) ($counts->get(
                DealStatus::Won->value,
            ) ?? 0),

            'lost' => (int) ($counts->get(
                DealStatus::Lost->value,
            ) ?? 0),
        ];
    }

    /**
     * @return Collection<int, Activity>
     */
    public function recentActivities(
        User $user,
        int $limit = 8,
    ): Collection {
        $query = Activity::query()
            ->with('user:id,name');

        if (! $user->isAdmin() && ! $user->isManager()) {
            $query->where(
                'user_id',
                $user->id,
            );
        }

        return $query
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * @return Collection<int, Task>
     */
    public function upcomingTasks(
        User $user,
        int $limit = 6,
    ): Collection {
        $query = Task::query()
            ->with([
                'assignedUser:id,name,email',
                'customer:id,name,company',
                'deal:id,title',
            ])
            ->where('completed', false)
            ->whereNotNull('due_date');

        if (! $user->isAdmin() && ! $user->isManager()) {
            $query->where(
                'assigned_user_id',
                $user->id,
            );
        }

        return $query
            ->orderBy('due_date')
            ->limit($limit)
            ->get();
    }

    /**
     * @return Builder<Customer>
     */
    private function visibleCustomersQuery(
        User $user,
    ): Builder {
        $query = Customer::query();

        if ($user->isAdmin() || $user->isManager()) {
            return $query;
        }

        return $query->where(function ($query) use ($user): void {
            $query
                ->whereHas(
                    'leads',
                    fn ($query) => $query->where(
                        'assigned_user_id',
                        $user->id,
                    ),
                )
                ->orWhereHas(
                    'deals',
                    fn ($query) => $query->where(
                        'assigned_user_id',
                        $user->id,
                    ),
                )
                ->orWhereHas(
                    'tasks',
                    fn ($query) => $query->where(
                        'assigned_user_id',
                        $user->id,
                    ),
                );
        });
    }

    /**
     * @return Builder<Lead>
     */
    private function visibleLeadsQuery(
        User $user,
    ): Builder {
        $query = Lead::query();

        if ($user->isAdmin() || $user->isManager()) {
            return $query;
        }

        return $query->where(
            'assigned_user_id',
            $user->id,
        );
    }

    /**
     * @return Builder<Deal>
     */
    private function visibleDealsQuery(
        User $user,
    ): Builder {
        $query = Deal::query();

        if ($user->isAdmin() || $user->isManager()) {
            return $query;
        }

        return $query->where(
            'assigned_user_id',
            $user->id,
        );
    }
}
