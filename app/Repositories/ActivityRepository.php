<?php

namespace App\Repositories;

use App\Data\Activities\ActivityData;
use App\Models\Activity;
use App\Repositories\Contracts\ActivityRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

final class ActivityRepository implements ActivityRepositoryInterface
{
    public function create(ActivityData $data): Activity
    {
        return Activity::create(
            $data->toArray(),
        );
    }

    /**
     * @return Collection<int, Activity>
     */
    public function recent(int $limit = 10): Collection
    {
        return Activity::query()
            ->with('user:id,name')
            ->latest()
            ->limit($limit)
            ->get();
    }
}
