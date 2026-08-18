<?php

namespace App\Repositories;

use App\Data\Leads\LeadData;
use App\Data\Leads\LeadFiltersData;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\User;
use App\Repositories\Contracts\LeadRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

final class LeadRepository implements LeadRepositoryInterface
{
    /**
     * @return LengthAwarePaginator<int, Lead>
     */
    public function paginateVisibleTo(
        User $user,
        LeadFiltersData $filters,
    ): LengthAwarePaginator {
        $query = Lead::query()
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
                    ->orWhereLike('source', $search)
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

    public function create(LeadData $data): Lead
    {
        return Lead::create(
            $data->toArray(),
        );
    }

    public function update(
        Lead $lead,
        LeadData $data,
    ): Lead {
        $lead->update(
            $data->toArray(),
        );

        return $lead->refresh();
    }

    public function delete(Lead $lead): void
    {
        $lead->delete();
    }

    public function loadDetails(Lead $lead): Lead
    {
        return $lead->load([
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
