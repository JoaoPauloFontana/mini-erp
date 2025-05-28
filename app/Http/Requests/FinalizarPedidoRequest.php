<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FinalizarPedidoRequest extends FormRequest
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
            'cliente_nome' => 'required|string|max:255',
            'cliente_email' => 'required|email|max:255',
            'cliente_telefone' => 'nullable|string|max:20',
            'cep' => 'required|string|size:8',
            'endereco' => 'required|string|max:1000'
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
            'cliente_nome.required' => 'O nome do cliente é obrigatório.',
            'cliente_nome.max' => 'O nome do cliente não pode ter mais de 255 caracteres.',
            'cliente_email.required' => 'O email do cliente é obrigatório.',
            'cliente_email.email' => 'O email deve ter um formato válido.',
            'cliente_email.max' => 'O email não pode ter mais de 255 caracteres.',
            'cliente_telefone.max' => 'O telefone não pode ter mais de 20 caracteres.',
            'cep.required' => 'O CEP é obrigatório.',
            'cep.size' => 'O CEP deve ter exatamente 8 dígitos.',
            'endereco.required' => 'O endereço é obrigatório.',
            'endereco.max' => 'O endereço não pode ter mais de 1000 caracteres.',
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
            'cliente_nome' => 'nome do cliente',
            'cliente_email' => 'email do cliente',
            'cliente_telefone' => 'telefone do cliente',
            'cep' => 'CEP',
            'endereco' => 'endereço',
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

        if ($this->has('cliente_telefone')) {
            $this->merge([
                'cliente_telefone' => preg_replace('/[^0-9]/', '', $this->cliente_telefone)
            ]);
        }
    }
}
