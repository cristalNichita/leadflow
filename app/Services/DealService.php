<?php

namespace App\Services;

use App\Data\Deals\DealData;
use App\Data\Deals\DealFiltersData;
use App\Models\Deal;
use App\Models\User;
use App\Repositories\Contracts\DealRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final readonly class DealService
{
    public function __construct(
        private DealRepositoryInterface $deals,
        private ActivityService $activities,
    ) {}

    /**
     * @return LengthAwarePaginator<int, Deal>
     */
    public function paginate(
        User $user,
        DealFiltersData $filters,
    ): LengthAwarePaginator {
        return $this->deals->paginateVisibleTo(
            $user,
            $filters,
        );
    }

    public function create(
        User $user,
        DealData $data,
    ): Deal {
        return DB::transaction(function () use (
            $user,
            $data,
        ): Deal {
            $deal = $this->deals->create(
                $data,
            );

            $this->activities->dealCreated(
                $user,
                $deal,
            );

            return $deal;
        });
    }

    public function update(
        User $user,
        Deal $deal,
        DealData $data,
    ): Deal {
        $previousStatus = $deal->status;

        if (! $user->isAdmin() && ! $user->isManager()) {
            $data = new DealData(
                title: $deal->title,
                customerId: $deal->customer_id,
                assignedUserId: $deal->assigned_user_id,
                value: (float) $deal->value,
                status: $data->status,
                expectedCloseDate: $deal->expected_close_date?->format(
                    'Y-m-d',
                ),
                notes: $data->notes,
            );
        }

        return DB::transaction(function () use (
            $user,
            $deal,
            $data,
            $previousStatus,
        ): Deal {
            $deal = $this->deals->update(
                $deal,
                $data,
            );

            if ($previousStatus !== $deal->status) {
                $this->activities->dealStatusChanged(
                    $user,
                    $deal,
                    $previousStatus,
                    $deal->status,
                );
            }

            return $deal;
        });
    }

    public function delete(
        User $user,
        Deal $deal,
    ): void {
        DB::transaction(function () use (
            $user,
            $deal,
        ): void {
            $title = $deal->title;

            $this->deals->delete(
                $deal,
            );

            $this->activities->dealDeleted(
                $user,
                $title,
            );
        });
    }

    public function details(Deal $deal): Deal
    {
        return $this->deals->loadDetails($deal);
    }

    /**
     * @return array{
     *     customers: Collection<int, array{id: int, name: string}>,
     *     users: Collection<int, array{id: int, name: string}>
     * }
     */
    public function formOptions(): array
    {
        return [
            'customers' => $this->deals->customerOptions(),
            'users' => $this->deals->userOptions(),
        ];
    }

    /**
     * @return Collection<int, array{id: int, name: string}>
     */
    public function assigneeOptions(): Collection
    {
        return $this->deals->userOptions();
    }
}
