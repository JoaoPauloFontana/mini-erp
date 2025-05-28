<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Produto extends Model
{
    protected $fillable = [
        'nome',
        'preco',
        'descricao',
        'ativo'
    ];

    protected $casts = [
        'preco' => 'decimal:2',
        'ativo' => 'boolean'
    ];

    public function variacoes(): HasMany
    {
        return $this->hasMany(ProdutoVariacao::class);
    }

    public function estoque(): HasMany
    {
        return $this->hasMany(Estoque::class);
    }

    public function pedidoItens(): HasMany
    {
        return $this->hasMany(PedidoItem::class);
    }

    public function getPrecoComVariacao($variacao_id = null)
    {
        if ($variacao_id) {
            $variacao = $this->variacoes()->find($variacao_id);
            return $this->preco + ($variacao ? $variacao->valor_adicional : 0);
        }
        return $this->preco;
    }

    public function getEstoqueDisponivel($variacao_id = null)
    {
        $estoque = $this->estoque()
            ->where('variacao_id', $variacao_id)
            ->first();

        return $estoque ? $estoque->quantidade : 0;
    }

    public function scopeAtivo($query)
    {
        return $query->where('ativo', true);
    }
}
