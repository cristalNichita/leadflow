<?php

namespace App\Http\Requests;

use App\Data\Tasks\TaskFiltersData;
use App\Enums\TaskPriority;
use App\Models\Task;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TaskIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('viewAny', Task::class) ?? false;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'search' => [
                'nullable',
                'string',
                'max:255',
            ],

            'priority' => [
                'nullable',
                Rule::enum(TaskPriority::class),
            ],

            'completed' => [
                'nullable',
                'boolean',
            ],

            'assigned_user_id' => [
                'nullable',
                'integer',
                'exists:users,id',
            ],
        ];
    }

    public function filters(): TaskFiltersData
    {
        /**
         * @var array{
         *     search?: string|null,
         *     priority?: string|null,
         *     completed?: bool|int|string|null,
         *     assigned_user_id?: int|null
         * } $validated
         */
        $validated = $this->validated();

        $search = isset($validated['search'])
            ? trim($validated['search'])
            : null;

        if ($search === '') {
            $search = null;
        }

        $completed = array_key_exists(
            'completed',
            $validated,
        )
            ? filter_var(
                $validated['completed'],
                FILTER_VALIDATE_BOOL,
            )
            : null;

        return new TaskFiltersData(
            search: $search,

            priority: isset($validated['priority'])
                ? TaskPriority::from($validated['priority'])
                : null,

            completed: $completed,

            assignedUserId: $validated['assigned_user_id'] ?? null,
        );
    }
}
