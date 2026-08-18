<?php

namespace App\Services;

use App\Data\Customers\CustomerData;
use App\Data\Customers\CustomerFiltersData;
use App\Models\Customer;
use App\Models\User;
use App\Repositories\Contracts\CustomerRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final readonly class CustomerService
{
    public function __construct(
        private CustomerRepositoryInterface $customers
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

    public function create(CustomerData $data): Customer
    {
        return $this->customers->create($data);
    }

    public function update(
        Customer $customer,
        CustomerData $data,
    ): Customer {
        return $this->customers->update(
            $customer,
            $data,
        );
    }

    public function delete(Customer $customer): void
    {
        $this->customers->delete($customer);
    }

    public function details(Customer $customer): Customer
    {
        return $this->customers->loadDetails($customer);
    }
}
