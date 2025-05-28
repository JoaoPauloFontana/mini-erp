<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProdutoRequest extends FormRequest
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
            'nome' => 'required|string|max:255',
            'preco' => 'required|numeric|min:0',
            'descricao' => 'nullable|string',
            'estoque_principal' => 'nullable|integer|min:0',
            'variacoes' => 'nullable|array',
            'variacoes.*.nome' => 'required_with:variacoes|string|max:255',
            'variacoes.*.valor_adicional' => 'nullable|numeric|min:0',
            'variacoes.*.estoque' => 'nullable|integer|min:0',
            'variacao_nome' => 'nullable|string|max:255',
            'variacao_valor_adicional' => 'nullable|numeric|min:0',
            'variacao_estoque' => 'nullable|integer|min:0',
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
            'nome.required' => 'O nome do produto é obrigatório.',
            'nome.max' => 'O nome do produto não pode ter mais de 255 caracteres.',
            'preco.required' => 'O preço é obrigatório.',
            'preco.numeric' => 'O preço deve ser um número válido.',
            'preco.min' => 'O preço deve ser maior que zero.',
            'estoque_principal.integer' => 'O estoque principal deve ser um número inteiro.',
            'estoque_principal.min' => 'O estoque principal não pode ser negativo.',
            'variacoes.*.nome.required_with' => 'O nome da variação é obrigatório.',
            'variacoes.*.nome.max' => 'O nome da variação não pode ter mais de 255 caracteres.',
            'variacoes.*.valor_adicional.numeric' => 'O valor adicional deve ser um número válido.',
            'variacoes.*.valor_adicional.min' => 'O valor adicional não pode ser negativo.',
            'variacoes.*.estoque.integer' => 'O estoque da variação deve ser um número inteiro.',
            'variacoes.*.estoque.min' => 'O estoque da variação não pode ser negativo.',
            'variacao_nome.max' => 'O nome da nova variação não pode ter mais de 255 caracteres.',
            'variacao_valor_adicional.numeric' => 'O valor adicional da nova variação deve ser um número válido.',
            'variacao_valor_adicional.min' => 'O valor adicional da nova variação não pode ser negativo.',
            'variacao_estoque.integer' => 'O estoque da nova variação deve ser um número inteiro.',
            'variacao_estoque.min' => 'O estoque da nova variação não pode ser negativo.',
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
            'nome' => 'nome do produto',
            'preco' => 'preço',
            'descricao' => 'descrição',
            'estoque_principal' => 'estoque principal',
            'variacoes.*.nome' => 'nome da variação',
            'variacoes.*.valor_adicional' => 'valor adicional',
            'variacoes.*.estoque' => 'estoque da variação',
            'variacao_nome' => 'nome da nova variação',
            'variacao_valor_adicional' => 'valor adicional da nova variação',
            'variacao_estoque' => 'estoque da nova variação',
        ];
    }
}
