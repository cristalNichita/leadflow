<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int|null $user_id
 * @property string $description
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read User|null $user
 */
class Activity extends Model
{
    protected $fillable = [
        'user_id',
        'description',
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
