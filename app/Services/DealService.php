<?php

namespace App\Services;

use App\Data\Deals\DealData;
use App\Data\Deals\DealFiltersData;
use App\Models\Deal;
use App\Models\User;
use App\Repositories\Contracts\DealRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

final readonly class DealService
{
    public function __construct(
        private DealRepositoryInterface $deals,
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

    public function create(DealData $data): Deal
    {
        return $this->deals->create($data);
    }

    public function update(
        User $user,
        Deal $deal,
        DealData $data,
    ): Deal {
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

        return $this->deals->update(
            $deal,
            $data,
        );
    }

    public function delete(Deal $deal): void
    {
        $this->deals->delete($deal);
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
