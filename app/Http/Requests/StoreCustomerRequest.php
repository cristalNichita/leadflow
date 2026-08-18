<?php

namespace App\Http\Requests;

use App\Data\Customers\CustomerData;
use App\Enums\CustomerStatus;
use App\Models\Customer;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCustomerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('create', Customer::class) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'company' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'status' => ['required', Rule::enum(CustomerStatus::class)],
            'notes' => ['nullable', 'string', 'max:5000'],
        ];
    }

    public function data($key = null, $default = null): CustomerData
    {
        /**
         * @var array{
         *     name: string,
         *     company?: string|null,
         *     email?: string|null,
         *     phone?: string|null,
         *     status: string,
         *     notes?: string|null
         * } $validated
         */
        $validated = $this->validated();

        return CustomerData::fromArray($validated);
    }
}
