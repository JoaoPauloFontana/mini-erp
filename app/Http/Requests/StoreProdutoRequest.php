<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Constants\SystemConstants;

class StoreProdutoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nome' => 'required|string|max:' . SystemConstants::MAX_STRING_LENGTH,
            'preco' => 'required|numeric|min:' . SystemConstants::MIN_NUMERIC_VALUE,
            'descricao' => 'nullable|string',
            'estoque_inicial' => 'nullable|integer|min:' . SystemConstants::MIN_QUANTITY,
            'variacoes' => 'nullable|array',
            'variacoes.*.nome' => 'required_with:variacoes|string|max:' . SystemConstants::MAX_STRING_LENGTH,
            'variacoes.*.valor_adicional' => 'nullable|numeric|min:' . SystemConstants::MIN_NUMERIC_VALUE,
            'variacoes.*.estoque' => 'nullable|integer|min:' . SystemConstants::MIN_QUANTITY,
        ];
    }

    public function messages(): array
    {
        return [
            'nome.required' => 'O nome do produto é obrigatório.',
            'nome.max' => 'O nome do produto não pode ter mais de ' . SystemConstants::MAX_STRING_LENGTH . ' caracteres.',
            'preco.required' => 'O preço é obrigatório.',
            'preco.numeric' => 'O preço deve ser um número válido.',
            'preco.min' => 'O preço deve ser maior que zero.',
            'estoque_inicial.integer' => 'O estoque inicial deve ser um número inteiro.',
            'estoque_inicial.min' => 'O estoque inicial não pode ser negativo.',
            'variacoes.*.nome.required_with' => 'O nome da variação é obrigatório.',
            'variacoes.*.nome.max' => 'O nome da variação não pode ter mais de ' . SystemConstants::MAX_STRING_LENGTH . ' caracteres.',
            'variacoes.*.valor_adicional.numeric' => 'O valor adicional deve ser um número válido.',
            'variacoes.*.valor_adicional.min' => 'O valor adicional não pode ser negativo.',
            'variacoes.*.estoque.integer' => 'O estoque da variação deve ser um número inteiro.',
            'variacoes.*.estoque.min' => 'O estoque da variação não pode ser negativo.',
        ];
    }

    public function attributes(): array
    {
        return [
            'nome' => 'nome do produto',
            'preco' => 'preço',
            'descricao' => 'descrição',
            'estoque_inicial' => 'estoque inicial',
            'variacoes.*.nome' => 'nome da variação',
            'variacoes.*.valor_adicional' => 'valor adicional',
            'variacoes.*.estoque' => 'estoque da variação',
        ];
    }
}
