<?php

namespace App\Http\Requests;

use App\Data\Deals\DealData;
use App\Enums\DealStatus;
use App\Models\Deal;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDealRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Deal::class) ?? false;
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

            'customer_id' => [
                'required',
                'integer',
                'exists:customers,id',
            ],

            'assigned_user_id' => [
                'nullable',
                'integer',
                'exists:users,id',
            ],

            'value' => [
                'required',
                'numeric',
                'min:0',
                'max:9999999999.99',
            ],

            'status' => [
                'required',
                Rule::enum(DealStatus::class),
            ],

            'expected_close_date' => [
                'nullable',
                'date_format:Y-m-d',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:5000',
            ],
        ];
    }

    public function data($key = null, $default = null): DealData
    {
        /**
         * @var array{
         *     title: string,
         *     customer_id: int,
         *     assigned_user_id?: int|null,
         *     value: numeric-string|int|float,
         *     status: string,
         *     expected_close_date?: string|null,
         *     notes?: string|null
         * } $validated
         */
        $validated = $this->validated();

        return DealData::fromArray($validated);
    }
}
