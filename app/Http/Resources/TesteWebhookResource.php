<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TesteWebhookResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'success' => $this->resource['success'],
            'message' => $this->resource['message'],
            'data' => $this->when(isset($this->resource['data']), $this->resource['data']),
            'error' => $this->when(isset($this->resource['error']), $this->resource['error']),
        ];
    }

    /**
     * Criar resposta de sucesso
     */
    public static function success(array $data, string $message = 'Operação realizada com sucesso'): self
    {
        return new self([
            'success' => true,
            'message' => $message,
            'data' => $data
        ]);
    }

    /**
     * Criar resposta de erro
     */
    public static function error(string $message, array $errors = null): self
    {
        $resource = [
            'success' => false,
            'message' => $message
        ];

        if ($errors) {
            $resource['error'] = $errors;
        }

        return new self($resource);
    }
}
