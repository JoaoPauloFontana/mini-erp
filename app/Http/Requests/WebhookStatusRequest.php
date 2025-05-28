<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WebhookStatusRequest extends FormRequest
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
            'status' => 'required|string|in:pendente,confirmado,enviado,entregue,cancelado'
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
            'pedido_id.exists' => 'O pedido especificado não existe.',
            'status.required' => 'O status é obrigatório.',
            'status.in' => 'O status deve ser: pendente, confirmado, enviado, entregue ou cancelado.',
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
