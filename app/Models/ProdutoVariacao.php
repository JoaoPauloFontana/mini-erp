<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProdutoVariacao extends Model
{
    protected $table = 'produto_variacoes';

    protected $fillable = [
        'produto_id',
        'nome',
        'valor_adicional',
        'ativo'
    ];

    protected $casts = [
        'valor_adicional' => 'decimal:2',
        'ativo' => 'boolean'
    ];

    public function produto(): BelongsTo
    {
        return $this->belongsTo(Produto::class);
    }

    public function estoque(): HasMany
    {
        return $this->hasMany(Estoque::class, 'variacao_id');
    }

    public function pedidoItens(): HasMany
    {
        return $this->hasMany(PedidoItem::class, 'variacao_id');
    }

    public function scopeAtivo($query)
    {
        return $query->where('ativo', true);
    }
}
