<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AtualizarCarrinhoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'chave' => 'required|string',
            'quantidade' => 'required|integer|min:1'
        ];
    }

    public function messages(): array
    {
        return [
            'chave.required' => 'A chave do item é obrigatória.',
            'quantidade.required' => 'A quantidade é obrigatória.',
            'quantidade.integer' => 'A quantidade deve ser um número inteiro.',
            'quantidade.min' => 'A quantidade deve ser pelo menos 1.',
        ];
    }

    public function attributes(): array
    {
        return [
            'chave' => 'item do carrinho',
            'quantidade' => 'quantidade',
        ];
    }
}
