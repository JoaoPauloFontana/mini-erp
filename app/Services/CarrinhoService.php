<?php

namespace App\Services;

use App\Models\Produto;
use App\Constants\SystemConstants;

class CarrinhoService
{
    public function calcularTotais(array $carrinho): array
    {
        $subtotal = array_sum(array_column($carrinho, 'subtotal'));
        $desconto = session(SystemConstants::SESSION_DESCONTO, SystemConstants::VALOR_PADRAO_ZERO);
        $subtotalComDesconto = $subtotal - $desconto;

        $frete = $this->calcularFrete($subtotalComDesconto);

        return [
            'subtotal' => $subtotal,
            'desconto' => $desconto,
            'frete' => $frete,
            'total' => $subtotalComDesconto + $frete
        ];
    }

    private function calcularFrete(float $valor): float
    {
        if ($valor >= SystemConstants::FRETE_GRATIS_LIMITE) {
            return SystemConstants::FRETE_GRATIS_VALOR;
        } elseif ($valor >= SystemConstants::FRETE_PROMOCIONAL_MIN && $valor <= SystemConstants::FRETE_PROMOCIONAL_MAX) {
            return SystemConstants::FRETE_PROMOCIONAL_VALOR;
        } else {
            return SystemConstants::FRETE_NORMAL_VALOR;
        }
    }

    public function gerarChaveItem(int $produtoId, ?int $variacaoId): string
    {
        return $produtoId . SystemConstants::CARRINHO_SEPARADOR . ($variacaoId ?? SystemConstants::CARRINHO_SEM_VARIACAO);
    }

    public function criarItemCarrinho(Produto $produto, ?int $variacaoId, int $quantidade): array
    {
        $preco = $produto->getPrecoComVariacao($variacaoId);
        $variacao = $variacaoId ? $produto->variacoes()->find($variacaoId) : null;

        return [
            'produto_id' => $produto->id,
            'variacao_id' => $variacaoId,
            'nome' => $produto->nome,
            'variacao_nome' => $variacao ? $variacao->nome : null,
            'preco_unitario' => $preco,
            'quantidade' => $quantidade,
            'subtotal' => $preco * $quantidade,
        ];
    }

    public function atualizarSubtotal(array &$item): void
    {
        $item['subtotal'] = $item['preco_unitario'] * $item['quantidade'];
    }

    public function calcularQuantidadeTotal(array $carrinho): int
    {
        return array_sum(array_column($carrinho, 'quantidade'));
    }

    public function carrinhoVazio(array $carrinho): bool
    {
        return empty($carrinho);
    }

    public function limparCarrinho(): void
    {
        session()->forget([
            SystemConstants::SESSION_CARRINHO,
            SystemConstants::SESSION_CUPOM_APLICADO,
            SystemConstants::SESSION_DESCONTO
        ]);
    }
}
