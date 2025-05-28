<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WebhookResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'success' => $this->resource['success'] ?? true,
            'message' => $this->resource['message'] ?? null,
            'error' => $this->resource['error'] ?? null,
            'data' => [
                'pedido_id' => $this->resource['pedido_id'] ?? null,
                'status' => $this->resource['status'] ?? null,
                'status_anterior' => $this->resource['status_anterior'] ?? null,
                'acao_executada' => $this->resource['acao_executada'] ?? null,
            ],
            'meta' => [
                'timestamp' => now()->toISOString(),
                'version' => '1.0',
                'endpoint' => $request->url(),
                'method' => $request->method()
            ]
        ];
    }

    public static function success($data = [], $message = null)
    {
        return new static([
            'success' => true,
            'message' => $message,
            ...$data
        ]);
    }

    public static function error($message, $data = [])
    {
        return new static([
            'success' => false,
            'error' => $message,
            ...$data
        ]);
    }
}
