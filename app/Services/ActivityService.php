<?php

namespace App\Services;

use App\Data\Activities\ActivityData;
use App\Enums\DealStatus;
use App\Enums\LeadStatus;
use App\Models\Activity;
use App\Models\Customer;
use App\Models\Deal;
use App\Models\Lead;
use App\Models\Task;
use App\Models\User;
use App\Repositories\Contracts\ActivityRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

final readonly class ActivityService
{
    public function __construct(
        private ActivityRepositoryInterface $activities,
    ) {}

    public function customerCreated(
        User $user,
        Customer $customer,
    ): Activity {
        return $this->record(
            $user,
            sprintf(
                '%s created customer "%s"',
                $user->name,
                $customer->name,
            ),
        );
    }

    public function customerUpdated(
        User $user,
        Customer $customer,
    ): Activity {
        return $this->record(
            $user,
            sprintf(
                '%s updated customer "%s"',
                $user->name,
                $customer->name,
            ),
        );
    }

    public function customerDeleted(
        User $user,
        string $customerName,
    ): Activity {
        return $this->record(
            $user,
            sprintf(
                '%s deleted customer "%s"',
                $user->name,
                $customerName,
            ),
        );
    }

    public function leadCreated(
        User $user,
        Lead $lead,
    ): Activity {
        return $this->record(
            $user,
            sprintf(
                '%s created lead "%s"',
                $user->name,
                $lead->title,
            ),
        );
    }

    public function leadStatusChanged(
        User $user,
        Lead $lead,
        LeadStatus $from,
        LeadStatus $to,
    ): Activity {
        return $this->record(
            $user,
            sprintf(
                '%s changed lead "%s" from %s to %s',
                $user->name,
                $lead->title,
                Str::headline($from->value),
                Str::headline($to->value),
            ),
        );
    }

    public function leadDeleted(
        User $user,
        string $leadTitle,
    ): Activity {
        return $this->record(
            $user,
            sprintf(
                '%s deleted lead "%s"',
                $user->name,
                $leadTitle,
            ),
        );
    }

    public function dealCreated(
        User $user,
        Deal $deal,
    ): Activity {
        return $this->record(
            $user,
            sprintf(
                '%s created deal "%s"',
                $user->name,
                $deal->title,
            ),
        );
    }

    public function dealStatusChanged(
        User $user,
        Deal $deal,
        DealStatus $from,
        DealStatus $to,
    ): Activity {
        return $this->record(
            $user,
            sprintf(
                '%s changed deal "%s" from %s to %s',
                $user->name,
                $deal->title,
                Str::headline($from->value),
                Str::headline($to->value),
            ),
        );
    }

    public function dealDeleted(
        User $user,
        string $dealTitle,
    ): Activity {
        return $this->record(
            $user,
            sprintf(
                '%s deleted deal "%s"',
                $user->name,
                $dealTitle,
            ),
        );
    }

    public function taskCreated(
        User $user,
        Task $task,
    ): Activity {
        return $this->record(
            $user,
            sprintf(
                '%s created task "%s"',
                $user->name,
                $task->title,
            ),
        );
    }

    public function taskCompleted(
        User $user,
        Task $task,
    ): Activity {
        return $this->record(
            $user,
            sprintf(
                '%s completed task "%s"',
                $user->name,
                $task->title,
            ),
        );
    }

    public function taskReopened(
        User $user,
        Task $task,
    ): Activity {
        return $this->record(
            $user,
            sprintf(
                '%s reopened task "%s"',
                $user->name,
                $task->title,
            ),
        );
    }

    public function taskDeleted(
        User $user,
        string $taskTitle,
    ): Activity {
        return $this->record(
            $user,
            sprintf(
                '%s deleted task "%s"',
                $user->name,
                $taskTitle,
            ),
        );
    }

    /**
     * @return Collection<int, Activity>
     */
    public function recent(int $limit = 10): Collection
    {
        return $this->activities->recent($limit);
    }

    private function record(
        User $user,
        string $description,
    ): Activity {
        return $this->activities->create(
            new ActivityData(
                userId: $user->id,
                description: $description,
            ),
        );
    }
}
