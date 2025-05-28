<?php

namespace App\Services;

use App\Models\Produto;
use App\Constants\SystemConstants;

class CarrinhoService
{
    /**
     * Calcular totais do carrinho
     */
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

    /**
     * Calcular frete baseado no valor
     */
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

    /**
     * Gerar chave única para item do carrinho
     */
    public function gerarChaveItem(int $produtoId, ?int $variacaoId): string
    {
        return $produtoId . SystemConstants::CARRINHO_SEPARADOR . ($variacaoId ?? SystemConstants::CARRINHO_SEM_VARIACAO);
    }

    /**
     * Criar item do carrinho
     */
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

    /**
     * Atualizar subtotal de um item
     */
    public function atualizarSubtotal(array &$item): void
    {
        $item['subtotal'] = $item['preco_unitario'] * $item['quantidade'];
    }

    /**
     * Calcular quantidade total do carrinho
     */
    public function calcularQuantidadeTotal(array $carrinho): int
    {
        return array_sum(array_column($carrinho, 'quantidade'));
    }

    /**
     * Verificar se carrinho está vazio
     */
    public function carrinhoVazio(array $carrinho): bool
    {
        return empty($carrinho);
    }

    /**
     * Limpar carrinho da sessão
     */
    public function limparCarrinho(): void
    {
        session()->forget([
            SystemConstants::SESSION_CARRINHO,
            SystemConstants::SESSION_CUPOM_APLICADO,
            SystemConstants::SESSION_DESCONTO
        ]);
    }
}
