<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AplicarCupomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'codigo' => 'required|string|max:50'
        ];
    }

    public function messages(): array
    {
        return [
            'codigo.required' => 'O código do cupom é obrigatório.',
            'codigo.max' => 'O código do cupom não pode ter mais de 50 caracteres.',
        ];
    }

    public function attributes(): array
    {
        return [
            'codigo' => 'código do cupom',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('codigo')) {
            $this->merge([
                'codigo' => strtoupper(trim($this->codigo))
            ]);
        }
    }
}
