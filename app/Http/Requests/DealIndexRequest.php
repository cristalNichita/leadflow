<?php

namespace App\Http\Requests;

use App\Data\Deals\DealFiltersData;
use App\Enums\DealStatus;
use App\Models\Deal;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DealIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('viewAny', Deal::class) ?? false;
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
                Rule::enum(DealStatus::class),
            ],

            'assigned_user_id' => [
                'nullable',
                'integer',
                'exists:users,id',
            ],
        ];
    }

    public function filters(): DealFiltersData
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

        return new DealFiltersData(
            search: $search,

            status: isset($validated['status'])
                ? DealStatus::from($validated['status'])
                : null,

            assignedUserId: $validated['assigned_user_id'] ?? null,
        );
    }
}
