<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Enums\TipoCupom;
use App\Constants\SystemConstants;

class Cupom extends Model
{
    protected $table = 'cupons';

    protected $fillable = [
        'codigo',
        'tipo',
        'valor',
        'valor_minimo',
        'data_inicio',
        'data_fim',
        'limite_uso',
        'usado',
        'ativo'
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'valor_minimo' => 'decimal:2',
        'data_inicio' => 'date',
        'data_fim' => 'date',
        'ativo' => 'boolean'
    ];

    public function pedidos(): HasMany
    {
        return $this->hasMany(Pedido::class);
    }

    public function validar($subtotal)
    {
        if (!$this->ativo) {
            return ['valido' => false, 'erro' => 'Cupom inativo'];
        }

        $hoje = now()->toDateString();
        if ($hoje < $this->data_inicio || $hoje > $this->data_fim) {
            return ['valido' => false, 'erro' => 'Cupom fora do período de validade'];
        }

        if ($subtotal < $this->valor_minimo) {
            return [
                'valido' => false,
                'erro' => 'Valor mínimo de R$ ' . number_format($this->valor_minimo, 2, ',', '.') . ' não atingido'
            ];
        }

        if ($this->limite_uso && $this->usado >= $this->limite_uso) {
            return ['valido' => false, 'erro' => 'Cupom esgotado'];
        }

        return ['valido' => true];
    }

    public function calcularDesconto($subtotal)
    {
        if ($this->tipo === TipoCupom::PERCENTUAL->value) {
            return ($subtotal * $this->valor) / 100;
        } else {
            return min($this->valor, $subtotal);
        }
    }

    public function scopeAtivo($query)
    {
        return $query->where('ativo', SystemConstants::PRODUTO_ATIVO_PADRAO);
    }

    public function scopeValido($query)
    {
        return $query->where('ativo', SystemConstants::PRODUTO_ATIVO_PADRAO)
                    ->where('data_inicio', '<=', now())
                    ->where('data_fim', '>=', now());
    }
}
