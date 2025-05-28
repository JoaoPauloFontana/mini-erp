<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AplicarCupomRequest extends FormRequest
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
            'codigo' => 'required|string|max:50'
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
            'codigo.required' => 'O código do cupom é obrigatório.',
            'codigo.max' => 'O código do cupom não pode ter mais de 50 caracteres.',
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
            'codigo' => 'código do cupom',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('codigo')) {
            $this->merge([
                'codigo' => strtoupper(trim($this->codigo))
            ]);
        }
    }
}
