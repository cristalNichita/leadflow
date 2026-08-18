<?php

namespace App\Repositories\Contracts;

use App\Data\Leads\LeadData;
use App\Data\Leads\LeadFiltersData;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface LeadRepositoryInterface
{
    /**
     * @return LengthAwarePaginator<int, Lead>
     */
    public function paginateVisibleTo(
        User $user,
        LeadFiltersData $filters,
    ): LengthAwarePaginator;

    public function create(LeadData $data): Lead;

    public function update(
        Lead $lead,
        LeadData $data,
    ): Lead;

    public function delete(Lead $lead): void;

    public function loadDetails(Lead $lead): Lead;

    /**
     * @return Collection<int, array{id: int, name: string}>
     */
    public function customerOptions(): Collection;

    /**
     * @return Collection<int, array{id: int, name: string}>
     */
    public function userOptions(): Collection;
}
