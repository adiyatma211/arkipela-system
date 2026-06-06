<?php

namespace App\Http\Requests;

use App\Enums\ClientStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ClientRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $clientId = $this->route('client')?->id;

        return [
            'company_name' => ['required', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'website' => ['nullable', 'url', 'max:255'],
            'pic_name' => ['nullable', 'string', 'max:255'],
            'pic_position' => ['nullable', 'string', 'max:255'],
            'pic_email' => ['nullable', 'email', 'max:255', Rule::unique('clients', 'pic_email')->ignore($clientId)],
            'pic_whatsapp' => ['nullable', 'string', 'max:50'],
            'interested_products' => ['nullable', 'string', 'max:255'],
            'target_quantity_kg' => ['nullable', 'numeric', 'min:0'],
            'target_price' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'max:20'],
            'preferred_incoterm' => ['nullable', 'string', 'max:255'],
            'preferred_payment_term' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::in(ClientStatus::values())],
            'source' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'company_name' => $this->string('company_name')->trim()->toString(),
            'country' => $this->filled('country') ? $this->string('country')->trim()->toString() : 'Indonesia',
            'city' => $this->string('city')->trim()->toString(),
            'website' => $this->string('website')->trim()->toString(),
            'pic_name' => $this->string('pic_name')->trim()->toString(),
            'pic_position' => $this->string('pic_position')->trim()->toString(),
            'pic_email' => $this->filled('pic_email') ? strtolower($this->string('pic_email')->trim()->toString()) : null,
            'pic_whatsapp' => $this->string('pic_whatsapp')->trim()->toString(),
            'interested_products' => $this->string('interested_products')->trim()->toString(),
            'currency' => $this->filled('currency') ? strtoupper($this->string('currency')->trim()->toString()) : 'USD',
            'preferred_incoterm' => $this->string('preferred_incoterm')->trim()->toString(),
            'preferred_payment_term' => $this->string('preferred_payment_term')->trim()->toString(),
            'source' => $this->string('source')->trim()->toString(),
        ]);
    }
}
