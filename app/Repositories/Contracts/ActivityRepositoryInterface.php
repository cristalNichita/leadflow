<?php

namespace App\Repositories\Contracts;

use App\Data\Activities\ActivityData;
use App\Models\Activity;
use Illuminate\Database\Eloquent\Collection;

interface ActivityRepositoryInterface
{
    public function create(ActivityData $data): Activity;

    /**
     * @return Collection<int, Activity>
     */
    public function recent(int $limit = 10): Collection;
}
