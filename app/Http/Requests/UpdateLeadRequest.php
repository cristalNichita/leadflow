<?php

namespace App\Http\Requests;

use App\Data\Leads\LeadData;
use App\Enums\LeadStatus;
use App\Models\Lead;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        $lead = $this->route('lead');

        return $lead instanceof Lead
            && ($this->user()?->can('update', $lead) ?? false);
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

            'estimated_value' => [
                'required',
                'numeric',
                'min:0',
                'max:9999999999.99',
            ],

            'source' => [
                'nullable',
                'string',
                'max:255',
            ],

            'status' => [
                'required',
                Rule::enum(LeadStatus::class),
            ],

            'notes' => [
                'nullable',
                'string',
                'max:5000',
            ],
        ];
    }

    public function data($key = null, $default = null): LeadData
    {
        /**
         * @var array{
         *     title: string,
         *     customer_id: int,
         *     assigned_user_id?: int|null,
         *     estimated_value: numeric-string|int|float,
         *     source?: string|null,
         *     status: string,
         *     notes?: string|null
         * } $validated
         */
        $validated = $this->validated();

        return LeadData::fromArray($validated);
    }
}
