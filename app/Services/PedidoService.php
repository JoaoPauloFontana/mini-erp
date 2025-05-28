<?php

namespace App\Services;

use App\Models\Pedido;
use App\Models\Cupom;
use App\Http\Requests\FinalizarPedidoRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class PedidoService
{
    protected $estoqueService;
    protected $carrinhoService;

    public function __construct(EstoqueService $estoqueService, CarrinhoService $carrinhoService)
    {
        $this->estoqueService = $estoqueService;
        $this->carrinhoService = $carrinhoService;
    }

    public function criarPedido(FinalizarPedidoRequest $request, array $carrinho): Pedido
    {
        return DB::transaction(function () use ($request, $carrinho) {
            $totais = $this->carrinhoService->calcularTotais($carrinho);
            $cupom = session('cupom_aplicado');

            $pedido = $this->criarRegistroPedido($request, $totais, $cupom);

            $this->processarItensCarrinho($pedido, $carrinho);

            $this->incrementarUsoCupom($cupom);

            $this->carrinhoService->limparCarrinho();

            $this->logConfirmacao($request->cliente_email);

            return $pedido;
        });
    }

    private function criarRegistroPedido(FinalizarPedidoRequest $request, array $totais, ?array $cupom): Pedido
    {
        return Pedido::create([
            'numero_pedido' => Pedido::gerarNumeroPedido(),
            'cliente_nome' => $request->cliente_nome,
            'cliente_email' => $request->cliente_email,
            'cliente_telefone' => $request->cliente_telefone,
            'cep' => $request->cep,
            'endereco' => $request->endereco,
            'subtotal' => $totais['subtotal'],
            'desconto' => $totais['desconto'],
            'frete' => $totais['frete'],
            'total' => $totais['total'],
            'cupom_id' => $cupom ? $cupom['id'] : null,
            'status' => 'pendente'
        ]);
    }

    private function processarItensCarrinho(Pedido $pedido, array $carrinho): void
    {
        foreach ($carrinho as $item) {
            if (!$this->estoqueService->verificarDisponibilidade(
                $item['produto_id'],
                $item['variacao_id'],
                $item['quantidade']
            )) {
                throw new Exception("Estoque insuficiente para: " . $item['nome']);
            }

            $pedido->itens()->create([
                'produto_id' => $item['produto_id'],
                'variacao_id' => $item['variacao_id'],
                'quantidade' => $item['quantidade'],
                'preco_unitario' => $item['preco_unitario'],
                'subtotal' => $item['subtotal']
            ]);

            $this->estoqueService->reduzirEstoque(
                $item['produto_id'],
                $item['variacao_id'],
                $item['quantidade']
            );
        }
    }

    private function incrementarUsoCupom(?array $cupom): void
    {
        if ($cupom) {
            Cupom::find($cupom['id'])->increment('usado');
        }
    }

    private function logConfirmacao(string $email): void
    {
        Log::info("Email de confirmação enviado para: " . $email);
    }

    public function cancelarPedido(Pedido $pedido): void
    {
        DB::transaction(function () use ($pedido) {
            foreach ($pedido->itens as $item) {
                $this->estoqueService->devolverEstoque(
                    $item->produto_id,
                    $item->variacao_id,
                    $item->quantidade
                );
            }

            $pedido->delete();
        });
    }

    public function atualizarStatus(Pedido $pedido, string $novoStatus): void
    {
        $pedido->update(['status' => $novoStatus]);
    }
}
