<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CarrinhoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'success' => $this->resource['success'] ?? true,
            'message' => $this->resource['message'] ?? null,
            'error' => $this->resource['error'] ?? null,
            'data' => $this->resource['data'] ?? [],
            'meta' => [
                'timestamp' => now()->toISOString(),
                'version' => '1.0'
            ]
        ];
    }

    public static function success($data = [], $message = null)
    {
        return new static([
            'success' => true,
            'message' => $message,
            'data' => $data
        ]);
    }

    public static function error($message, $data = [])
    {
        return new static([
            'success' => false,
            'error' => $message,
            'data' => $data
        ]);
    }
}
