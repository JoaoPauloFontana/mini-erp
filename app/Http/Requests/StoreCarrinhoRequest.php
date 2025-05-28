<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCarrinhoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'produto_id' => 'required|exists:produtos,id',
            'quantidade' => 'required|integer|min:1',
            'variacao_id' => 'nullable|exists:produto_variacoes,id'
        ];
    }

    public function messages(): array
    {
        return [
            'produto_id.required' => 'O produto é obrigatório.',
            'produto_id.exists' => 'O produto selecionado não existe.',
            'quantidade.required' => 'A quantidade é obrigatória.',
            'quantidade.integer' => 'A quantidade deve ser um número inteiro.',
            'quantidade.min' => 'A quantidade deve ser pelo menos 1.',
            'variacao_id.exists' => 'A variação selecionada não existe.',
        ];
    }

    public function attributes(): array
    {
        return [
            'produto_id' => 'produto',
            'quantidade' => 'quantidade',
            'variacao_id' => 'variação',
        ];
    }
}
