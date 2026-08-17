<?php

namespace App\Models;

use App\Enums\DealStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Deal extends Model
{
    protected $fillable = [
        'title',
        'customer_id',
        'assigned_user_id',
        'value',
        'status',
        'expected_close_date',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'decimal:2',
            'status' => DealStatus::class,
            'expected_close_date' => 'date',
        ];
    }

    /**
     * @return BelongsTo<Customer, $this>
     **/
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * @return BelongsTo<User, $this>
     **/
    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    /**
     * @return HasMany<Task, $this>
     **/
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }
}
