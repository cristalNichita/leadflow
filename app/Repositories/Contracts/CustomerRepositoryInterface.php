<?php

namespace App\Repositories\Contracts;

use App\Data\Customers\CustomerData;
use App\Data\Customers\CustomerFiltersData;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface CustomerRepositoryInterface
{
    /**
     * @return LengthAwarePaginator<int, Customer>
     */
    public function paginateVisibleTo(
        User $user,
        CustomerFiltersData $filters,
    ): LengthAwarePaginator;

    public function create(CustomerData $data): Customer;

    public function update(
        Customer $customer,
        CustomerData $data,
    ): Customer;

    public function delete(Customer $customer): void;

    public function loadDetails(Customer $customer): Customer;
}
