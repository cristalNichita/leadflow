<?php

namespace App\Http\Requests;

use App\Data\Leads\LeadFiltersData;
use App\Enums\LeadStatus;
use App\Models\Lead;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LeadIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('viewAny', Lead::class) ?? false;
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

            'status' => [
                'nullable',
                Rule::enum(LeadStatus::class),
            ],

            'assigned_user_id' => [
                'nullable',
                'integer',
                'exists:users,id',
            ],
        ];
    }

    public function filters(): LeadFiltersData
    {
        /**
         * @var array{
         *     search?: string|null,
         *     status?: string|null,
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

        return new LeadFiltersData(
            search: $search,

            status: isset($validated['status'])
                ? LeadStatus::from($validated['status'])
                : null,

            assignedUserId: $validated['assigned_user_id'] ?? null,
        );
    }
}
