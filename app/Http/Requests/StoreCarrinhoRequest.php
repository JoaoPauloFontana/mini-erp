<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCarrinhoRequest extends FormRequest
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
            'produto_id' => 'required|exists:produtos,id',
            'quantidade' => 'required|integer|min:1',
            'variacao_id' => 'nullable|exists:produto_variacoes,id'
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
            'produto_id.required' => 'O produto é obrigatório.',
            'produto_id.exists' => 'O produto selecionado não existe.',
            'quantidade.required' => 'A quantidade é obrigatória.',
            'quantidade.integer' => 'A quantidade deve ser um número inteiro.',
            'quantidade.min' => 'A quantidade deve ser pelo menos 1.',
            'variacao_id.exists' => 'A variação selecionada não existe.',
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
            'produto_id' => 'produto',
            'quantidade' => 'quantidade',
            'variacao_id' => 'variação',
        ];
    }
}
