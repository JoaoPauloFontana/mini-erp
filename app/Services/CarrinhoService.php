<?php

namespace App\Services;

use App\Models\Produto;

class CarrinhoService
{
    /**
     * Calcular totais do carrinho
     */
    public function calcularTotais(array $carrinho): array
    {
        $subtotal = array_sum(array_column($carrinho, 'subtotal'));
        $desconto = session('desconto', 0);
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
        if ($valor >= 200) {
            return 0; 
        } elseif ($valor >= 52 && $valor <= 166.59) {
            return 15;
        } else {
            return 20;
        }
    }

    /**
     * Gerar chave única para item do carrinho
     */
    public function gerarChaveItem(int $produtoId, ?int $variacaoId): string
    {
        return $produtoId . '_' . ($variacaoId ?? 'sem_variacao');
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
        session()->forget(['carrinho', 'cupom_aplicado', 'desconto']);
    }
}
