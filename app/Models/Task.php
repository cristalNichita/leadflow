<?php

namespace App\Models;

use App\Enums\TaskPriority;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    protected $fillable = [
        'title',
        'description',
        'assigned_user_id',
        'customer_id',
        'deal_id',
        'priority',
        'due_date',
        'completed',
    ];

    protected function casts(): array
    {
        return [
            'priority' => TaskPriority::class,
            'due_date' => 'date',
            'completed' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     **/
    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    /**
     * @return BelongsTo<Customer, $this>
     **/
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * @return BelongsTo<Deal, $this>
     **/
    public function deal(): BelongsTo
    {
        return $this->belongsTo(Deal::class);
    }
}
