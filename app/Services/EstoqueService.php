<?php

namespace App\Services;

use App\Models\Estoque;

class EstoqueService
{
    /**
     * Criar ou atualizar estoque para um produto
     */
    public function criarOuAtualizarEstoque(int $produtoId, ?int $variacaoId, int $quantidade): ?Estoque
    {
        if ($quantidade <= 0) {
            return null;
        }

        return Estoque::updateOrCreate(
            [
                'produto_id' => $produtoId,
                'variacao_id' => $variacaoId,
            ],
            [
                'quantidade' => $quantidade,
            ]
        );
    }

    /**
     * Verificar se há estoque suficiente
     */
    public function verificarDisponibilidade(int $produtoId, ?int $variacaoId, int $quantidadeDesejada): bool
    {
        $estoque = Estoque::where('produto_id', $produtoId)
            ->where('variacao_id', $variacaoId)
            ->first();

        return $estoque && $estoque->verificarDisponibilidade($quantidadeDesejada);
    }

    /**
     * Reduzir estoque
     */
    public function reduzirEstoque(int $produtoId, ?int $variacaoId, int $quantidade): bool
    {
        $estoque = Estoque::where('produto_id', $produtoId)
            ->where('variacao_id', $variacaoId)
            ->first();

        if (!$estoque || !$estoque->verificarDisponibilidade($quantidade)) {
            return false;
        }

        $estoque->reduzirEstoque($quantidade);
        return true;
    }

    /**
     * Devolver estoque
     */
    public function devolverEstoque(int $produtoId, ?int $variacaoId, int $quantidade): bool
    {
        $estoque = Estoque::where('produto_id', $produtoId)
            ->where('variacao_id', $variacaoId)
            ->first();

        if (!$estoque) {
            return false;
        }

        $estoque->aumentarEstoque($quantidade);
        return true;
    }
}
