<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Enums\StatusPedido;

class Pedido extends Model
{
    protected $fillable = [
        'numero_pedido',
        'cliente_nome',
        'cliente_email',
        'cliente_telefone',
        'cep',
        'endereco',
        'subtotal',
        'desconto',
        'frete',
        'total',
        'cupom_id',
        'status'
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'desconto' => 'decimal:2',
        'frete' => 'decimal:2',
        'total' => 'decimal:2'
    ];

    public function cupom(): BelongsTo
    {
        return $this->belongsTo(Cupom::class);
    }

    public function itens(): HasMany
    {
        return $this->hasMany(PedidoItem::class);
    }

    public static function gerarNumeroPedido()
    {
        return 'PED' . date('Ymd') . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
    }

    public function scopePorStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}
