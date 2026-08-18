<?php

namespace App\Http\Requests;

use App\Data\Tasks\TaskData;
use App\Enums\TaskPriority;
use App\Models\Task;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Task::class) ?? false;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'assigned_user_id' => [
                'nullable',
                'integer',
                'exists:users,id',
            ],

            'customer_id' => [
                'nullable',
                'integer',
                'exists:customers,id',
            ],

            'deal_id' => [
                'nullable',
                'integer',
                'exists:deals,id',
            ],

            'priority' => [
                'required',
                Rule::enum(TaskPriority::class),
            ],

            'due_date' => [
                'nullable',
                'date_format:Y-m-d',
            ],

            'completed' => [
                'sometimes',
                'boolean',
            ],
        ];
    }

    /**
     * @return array<int, callable(Validator): void>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $customerId = $this->input('customer_id');
                $dealId = $this->input('deal_id');

                if ($customerId === null && $dealId === null) {
                    $validator->errors()->add(
                        'customer_id',
                        'Select either a customer or a deal.',
                    );

                    return;
                }

                if ($customerId !== null && $dealId !== null) {
                    $validator->errors()->add(
                        'deal_id',
                        'A task cannot belong to both a customer and a deal.',
                    );
                }
            },
        ];
    }

    public function data($key = null, $default = null): TaskData
    {
        /**
         * @var array{
         *     title: string,
         *     description?: string|null,
         *     assigned_user_id?: int|null,
         *     customer_id?: int|null,
         *     deal_id?: int|null,
         *     priority: string,
         *     due_date?: string|null,
         *     completed?: bool|int|string
         * } $validated
         */
        $validated = $this->validated();

        return TaskData::fromArray($validated);
    }
}
