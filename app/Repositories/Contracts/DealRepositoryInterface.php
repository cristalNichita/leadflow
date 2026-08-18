<?php

namespace App\Repositories\Contracts;

use App\Data\Deals\DealData;
use App\Data\Deals\DealFiltersData;
use App\Models\Deal;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface DealRepositoryInterface
{
    /**
     * @return LengthAwarePaginator<int, Deal>
     */
    public function paginateVisibleTo(
        User $user,
        DealFiltersData $filters,
    ): LengthAwarePaginator;

    public function create(DealData $data): Deal;

    public function update(
        Deal $deal,
        DealData $data,
    ): Deal;

    public function delete(Deal $deal): void;

    public function loadDetails(Deal $deal): Deal;

    /**
     * @return Collection<int, array{id: int, name: string}>
     */
    public function customerOptions(): Collection;

    /**
     * @return Collection<int, array{id: int, name: string}>
     */
    public function userOptions(): Collection;
}
