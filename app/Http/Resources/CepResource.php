<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CepResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'success' => $this->resource['success'] ?? true,
            'message' => $this->resource['message'] ?? null,
            'error' => $this->resource['error'] ?? null,
            'data' => [
                'cep' => $this->when(isset($this->resource['cep_data']), function () {
                    $data = $this->resource['cep_data'];
                    return [
                        'cep' => $data['cep'] ?? null,
                        'logradouro' => $data['logradouro'] ?? null,
                        'complemento' => $data['complemento'] ?? null,
                        'bairro' => $data['bairro'] ?? null,
                        'localidade' => $data['localidade'] ?? null,
                        'uf' => $data['uf'] ?? null,
                        'ibge' => $data['ibge'] ?? null,
                        'gia' => $data['gia'] ?? null,
                        'ddd' => $data['ddd'] ?? null,
                        'siafi' => $data['siafi'] ?? null,
                    ];
                }),
            ],
            'meta' => [
                'timestamp' => now()->toISOString(),
                'version' => '1.0',
                'provider' => 'ViaCEP'
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
