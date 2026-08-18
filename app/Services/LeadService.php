<?php

namespace App\Services;

use App\Data\Leads\LeadData;
use App\Data\Leads\LeadFiltersData;
use App\Models\Lead;
use App\Models\User;
use App\Repositories\Contracts\LeadRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

final readonly class LeadService
{
    public function __construct(
        private LeadRepositoryInterface $leads,
    ) {}

    /**
     * @return LengthAwarePaginator<int, Lead>
     */
    public function paginate(
        User $user,
        LeadFiltersData $filters,
    ): LengthAwarePaginator {
        return $this->leads->paginateVisibleTo(
            $user,
            $filters,
        );
    }

    public function create(LeadData $data): Lead
    {
        return $this->leads->create($data);
    }

    public function update(
        User $user,
        Lead $lead,
        LeadData $data,
    ): Lead {
        if (! $user->isAdmin() && ! $user->isManager()) {
            $data = new LeadData(
                title: $lead->title,
                customerId: $lead->customer_id,
                assignedUserId: $lead->assigned_user_id,
                estimatedValue: (float) $lead->estimated_value,
                source: $lead->source,
                status: $data->status,
                notes: $data->notes,
            );
        }

        return $this->leads->update(
            $lead,
            $data,
        );
    }

    public function delete(Lead $lead): void
    {
        $this->leads->delete($lead);
    }

    public function details(Lead $lead): Lead
    {
        return $this->leads->loadDetails($lead);
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
            'customers' => $this->leads->customerOptions(),
            'users' => $this->leads->userOptions(),
        ];
    }

    /**
     * @return Collection<int, array{id: int, name: string}>
     */
    public function assigneeOptions(): Collection
    {
        return $this->leads->userOptions();
    }
}
