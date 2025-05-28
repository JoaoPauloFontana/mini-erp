<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\StatusPedido;

class TesteWebhookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pedido_id' => 'required|integer|exists:pedidos,id',
            'status' => 'required|string|in:' . implode(',', StatusPedido::values())
        ];
    }

    public function messages(): array
    {
        return [
            'pedido_id.required' => 'O ID do pedido é obrigatório.',
            'pedido_id.integer' => 'O ID do pedido deve ser um número inteiro.',
            'status.required' => 'O status é obrigatório.',
        ];
    }

    public function attributes(): array
    {
        return [
            'pedido_id' => 'ID do pedido',
            'status' => 'status',
        ];
    }
}
