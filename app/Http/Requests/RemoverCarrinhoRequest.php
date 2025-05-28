<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RemoverCarrinhoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'chave' => 'required|string'
        ];
    }

    public function messages(): array
    {
        return [
            'chave.required' => 'A chave do item é obrigatória.',
        ];
    }

    public function attributes(): array
    {
        return [
            'chave' => 'item do carrinho',
        ];
    }
}
