<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VerificarCepRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'cep' => 'required|string|size:8'
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'cep.required' => 'O CEP é obrigatório.',
            'cep.size' => 'O CEP deve ter exatamente 8 dígitos.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'cep' => 'CEP',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('cep')) {
            $this->merge([
                'cep' => preg_replace('/[^0-9]/', '', $this->cep)
            ]);
        }
    }
}
