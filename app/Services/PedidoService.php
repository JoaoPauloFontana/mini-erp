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
    protected EstoqueService $estoqueService;

    protected CarrinhoService $carrinhoService;

    protected EmailService $emailService;

    public function __construct(
        EstoqueService $estoqueService,
        CarrinhoService $carrinhoService,
        EmailService $emailService
    ) {
        $this->estoqueService = $estoqueService;
        $this->carrinhoService = $carrinhoService;
        $this->emailService = $emailService;
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

            try {
                $this->emailService->enviarConfirmacaoPedido($pedido);
                Log::info('E-mail de confirmação enviado com sucesso', [
                    'pedido_id' => $pedido->id,
                    'cliente_email' => $request->cliente_email
                ]);
            } catch (Exception $e) {
                Log::error('Falha ao enviar e-mail de confirmação', [
                    'pedido_id' => $pedido->id,
                    'cliente_email' => $request->cliente_email,
                    'error' => $e->getMessage()
                ]);
            }

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
        $statusAnterior = $pedido->status;
        $pedido->update(['status' => $novoStatus]);

        try {
            $this->emailService->enviarAtualizacaoStatus($pedido, $statusAnterior, $novoStatus);
        } catch (Exception $e) {
            Log::error('Falha ao enviar e-mail de atualização de status', [
                'pedido_id' => $pedido->id,
                'status_anterior' => $statusAnterior,
                'novo_status' => $novoStatus,
                'error' => $e->getMessage()
            ]);
        }
    }
}
