<?php

namespace App\Repositories;

use App\Data\Customers\CustomerData;
use App\Data\Customers\CustomerFiltersData;
use App\Models\Customer;
use App\Models\User;
use App\Repositories\Contracts\CustomerRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class CustomerRepository implements CustomerRepositoryInterface
{
    /**
     * @return LengthAwarePaginator<int, Customer>
     */
    public function paginateVisibleTo(User $user, CustomerFiltersData $filters): LengthAwarePaginator
    {
        $query = Customer::query()
            ->withCount([
                'leads',
                'deals',
                'tasks',
            ]);

        if (! $user->isAdmin() && ! $user->isManager()) {
            $query->where(function ($query) use ($user): void {
                $query
                    ->whereHas('leads', fn ($query) => $query->where('assigned_user_id', $user->id))
                    ->orWhereHas('deals', fn ($query) => $query->where('assigned_user_id', $user->id))
                    ->orWhereHas('tasks', fn ($query) => $query->where('assigned_user_id', $user->id));
            });
        }

        if ($filters->search !== null) {
            $search = '%'.$filters->search.'%';

            $query->where(function ($query) use ($search): void {
                $query
                    ->whereLike('name', $search)
                    ->orWhereLike('company', $search)
                    ->orWhereLike('email', $search)
                    ->orWhereLike('phone', $search);
            });
        }

        if ($filters->status !== null) {
            $query->where('status', $filters->status);
        }

        return $query
            ->orderBy('name')
            ->paginate($filters->perPage)
            ->withQueryString();
    }

    public function create(CustomerData $data): Customer
    {
        return Customer::create($data->toArray());
    }

    public function update(
        Customer $customer,
        CustomerData $data,
    ): Customer {
        $customer->update($data->toArray());

        return $customer->refresh();
    }

    public function delete(Customer $customer): void
    {
        $customer->delete();
    }

    public function loadDetails(Customer $customer): Customer
    {
        return $customer
            ->loadCount([
                'leads',
                'deals',
                'tasks',
            ]);
    }
}
