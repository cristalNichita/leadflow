<?php

namespace App\Services;

use App\Data\Leads\LeadData;
use App\Data\Leads\LeadFiltersData;
use App\Models\Lead;
use App\Models\User;
use App\Repositories\Contracts\LeadRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final readonly class LeadService
{
    public function __construct(
        private LeadRepositoryInterface $leads,
        private ActivityService $activities,
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

    public function create(
        User $user,
        LeadData $data,
    ): Lead {
        return DB::transaction(function () use (
            $user,
            $data,
        ): Lead {
            $lead = $this->leads->create(
                $data,
            );

            $this->activities->leadCreated(
                $user,
                $lead,
            );

            return $lead;
        });
    }

    public function update(
        User $user,
        Lead $lead,
        LeadData $data,
    ): Lead {
        $previousStatus = $lead->status;

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

        return DB::transaction(function () use (
            $user,
            $lead,
            $data,
            $previousStatus,
        ): Lead {
            $lead = $this->leads->update(
                $lead,
                $data,
            );

            if ($previousStatus !== $lead->status) {
                $this->activities->leadStatusChanged(
                    $user,
                    $lead,
                    $previousStatus,
                    $lead->status,
                );
            }

            return $lead;
        });
    }

    public function delete(
        User $user,
        Lead $lead,
    ): void {
        DB::transaction(function () use (
            $user,
            $lead,
        ): void {
            $title = $lead->title;

            $this->leads->delete(
                $lead,
            );

            $this->activities->leadDeleted(
                $user,
                $title,
            );
        });
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
