<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Estoque extends Model
{
    protected $table = 'estoque';

    protected $fillable = [
        'produto_id',
        'variacao_id',
        'quantidade',
        'quantidade_minima'
    ];

    public function produto(): BelongsTo
    {
        return $this->belongsTo(Produto::class);
    }

    public function variacao(): BelongsTo
    {
        return $this->belongsTo(ProdutoVariacao::class, 'variacao_id');
    }

    public function verificarDisponibilidade($quantidade)
    {
        return $this->quantidade >= $quantidade;
    }

    public function reduzirEstoque($quantidade)
    {
        if ($this->quantidade >= $quantidade) {
            $this->quantidade -= $quantidade;
            return $this->save();
        }
        return false;
    }

    public function aumentarEstoque($quantidade)
    {
        $this->quantidade += $quantidade;
        return $this->save();
    }
}
