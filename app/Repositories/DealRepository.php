<?php

namespace App\Repositories;

use App\Data\Deals\DealData;
use App\Data\Deals\DealFiltersData;
use App\Models\Customer;
use App\Models\Deal;
use App\Models\User;
use App\Repositories\Contracts\DealRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

final class DealRepository implements DealRepositoryInterface
{
    /**
     * @return LengthAwarePaginator<int, Deal>
     */
    public function paginateVisibleTo(
        User $user,
        DealFiltersData $filters,
    ): LengthAwarePaginator {
        $query = Deal::query()
            ->with([
                'customer:id,name,company',
                'assignedUser:id,name,email',
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
                    ->orWhereHas(
                        'customer',
                        fn ($query) => $query
                            ->whereLike('name', $search)
                            ->orWhereLike('company', $search),
                    );
            });
        }

        if ($filters->status !== null) {
            $query->where(
                'status',
                $filters->status->value,
            );
        }

        if ($filters->assignedUserId !== null) {
            $query->where(
                'assigned_user_id',
                $filters->assignedUserId,
            );
        }

        return $query
            ->latest()
            ->paginate($filters->perPage)
            ->withQueryString();
    }

    public function create(DealData $data): Deal
    {
        return Deal::create(
            $data->toArray(),
        );
    }

    public function update(
        Deal $deal,
        DealData $data,
    ): Deal {
        $deal->update(
            $data->toArray(),
        );

        return $deal->refresh();
    }

    public function delete(Deal $deal): void
    {
        $deal->delete();
    }

    public function loadDetails(Deal $deal): Deal
    {
        return $deal->load([
            'customer:id,name,company,email,phone,status',
            'assignedUser:id,name,email',
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
}
