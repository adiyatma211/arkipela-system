<?php

namespace App\Http\Requests;

use App\Models\ArkipelaParameter;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ArkipelaParameterRequest extends FormRequest
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
        /** @var ArkipelaParameter|null $parameter */
        $parameter = $this->route('parameter');

        return [
            'group_key' => ['required', 'string', 'max:100'],
            'code' => [
                'required',
                'string',
                'max:100',
                Rule::unique('arkipela_parameters', 'code')
                    ->where(fn ($query) => $query->where('group_key', $this->input('group_key')))
                    ->ignore($parameter?->id),
            ],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'attributes_json' => ['nullable', 'json'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $groupKey = str((string) $this->input('group_key'))
            ->trim()
            ->lower()
            ->replace(' ', '_')
            ->replace('-', '_')
            ->toString();

        $code = str((string) $this->input('code'))
            ->trim()
            ->upper()
            ->replace(' ', '_')
            ->replace('-', '_')
            ->toString();

        $attributesJson = trim((string) $this->input('attributes_json', ''));

        $this->merge([
            'group_key' => $groupKey,
            'code' => $code,
            'name' => $this->string('name')->trim()->toString(),
            'description' => $this->string('description')->trim()->toString(),
            'sort_order' => $this->input('sort_order') !== null && $this->input('sort_order') !== ''
                ? (int) $this->input('sort_order')
                : 0,
            'is_active' => $this->boolean('is_active'),
            'attributes_json' => $attributesJson !== '' ? $attributesJson : null,
        ]);
    }
}
