<?php

namespace App\Http\Requests;

use App\Data\Customers\CustomerFiltersData;
use App\Enums\CustomerStatus;
use App\Models\Customer;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CustomerIndexRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('viewAny', Customer::class) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:255'],
            'status' => [
                'nullable',
                Rule::enum(CustomerStatus::class),
            ],
        ];
    }

    public function filters(): CustomerFiltersData
    {
        /**
         * @var array{
         *     search?: string|null,
         *     status?: string|null
         * } $validated
         */
        $validated = $this->validated();

        $search = isset($validated['search'])
            ? trim($validated['search'])
            : null;

        if ($search === '') {
            $search = null;
        }

        $status = isset($validated['status'])
            ? CustomerStatus::from($validated['status'])
            : null;

        return new CustomerFiltersData(
            search: $search,
            status: $status,
        );
    }
}
