<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\StatusPedido;

class TesteWebhookRequest extends FormRequest
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
            'pedido_id' => 'required|integer|exists:pedidos,id',
            'status' => 'required|string|in:' . implode(',', StatusPedido::values())
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
            'pedido_id.required' => 'O ID do pedido é obrigatório.',
            'pedido_id.integer' => 'O ID do pedido deve ser um número inteiro.',
            'status.required' => 'O status é obrigatório.',
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
            'pedido_id' => 'ID do pedido',
            'status' => 'status',
        ];
    }
}
