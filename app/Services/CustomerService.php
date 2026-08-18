<?php

namespace App\Services;

use App\Data\Customers\CustomerData;
use App\Data\Customers\CustomerFiltersData;
use App\Models\Customer;
use App\Models\User;
use App\Repositories\Contracts\CustomerRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

final readonly class CustomerService
{
    public function __construct(
        private CustomerRepositoryInterface $customers,
        private ActivityService $activities,
    ) {}

    /**
     * @return LengthAwarePaginator<int, Customer>
     */
    public function paginate(
        User $user,
        CustomerFiltersData $filters,
    ): LengthAwarePaginator {
        return $this->customers->paginateVisibleTo(
            $user,
            $filters,
        );
    }

    public function create(
        User $user,
        CustomerData $data,
    ): Customer {
        return DB::transaction(function () use (
            $user,
            $data,
        ): Customer {
            $customer = $this->customers->create(
                $data,
            );

            $this->activities->customerCreated(
                $user,
                $customer,
            );

            return $customer;
        });
    }

    public function update(
        User $user,
        Customer $customer,
        CustomerData $data,
    ): Customer {
        return DB::transaction(function () use (
            $user,
            $customer,
            $data,
        ): Customer {
            $customer = $this->customers->update(
                $customer,
                $data,
            );

            $this->activities->customerUpdated(
                $user,
                $customer,
            );

            return $customer;
        });
    }

    public function delete(
        User $user,
        Customer $customer,
    ): void {
        DB::transaction(function () use (
            $user,
            $customer,
        ): void {
            $name = $customer->name;

            $this->customers->delete(
                $customer,
            );

            $this->activities->customerDeleted(
                $user,
                $name,
            );
        });
    }

    public function details(Customer $customer): Customer
    {
        return $this->customers->loadDetails(
            $customer,
        );
    }
}
