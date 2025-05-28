<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\Estoque;
use App\Http\Requests\WebhookStatusRequest;
use App\Http\Requests\TesteWebhookRequest;
use App\Http\Resources\WebhookResource;
use App\Services\PedidoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class WebhookController extends Controller
{
    protected PedidoService $pedidoService;

    public function __construct(PedidoService $pedidoService)
    {
        $this->pedidoService = $pedidoService;
    }

    public function receberStatus(WebhookStatusRequest $request)
    {

        $pedido = Pedido::find($request->pedido_id);

        if (!$pedido) {
            return WebhookResource::error('Pedido não encontrado')->response()->setStatusCode(404);
        }

        try {
            $statusAnterior = $pedido->status;
            $acaoExecutada = null;

            if ($request->status === 'cancelado') {
                $this->pedidoService->cancelarPedido($pedido);
                $acaoExecutada = 'pedido_removido_estoque_devolvido';
                Log::info("Pedido {$pedido->id} cancelado e removido via webhook");
            } else {
                $this->pedidoService->atualizarStatus($pedido, $request->status);
                $acaoExecutada = 'status_atualizado';
                Log::info("Status do pedido {$pedido->id} atualizado para {$request->status} via webhook");
            }

            return WebhookResource::success([
                'pedido_id' => $request->pedido_id,
                'status' => $request->status,
                'status_anterior' => $statusAnterior,
                'acao_executada' => $acaoExecutada
            ], $request->status === 'cancelado' ? 'Pedido cancelado e removido' : 'Status atualizado');

        } catch (\Exception $e) {
            Log::error("Erro no webhook: " . $e->getMessage());
            return WebhookResource::error('Erro interno do servidor')->response()->setStatusCode(500);
        }
    }

    public function testarWebhookForm()
    {
        $pedidos = Pedido::orderBy('created_at', 'desc')->take(10)->get();
        return view('webhook.teste', compact('pedidos'));
    }

    public function testarWebhookPost(TesteWebhookRequest $request)
    {
        Log::info("Teste webhook POST recebido", $request->all());

        try {
            $pedidoId = $request->pedido_id;
            $novoStatus = $request->status;

            Log::info("Processando webhook - Pedido ID: {$pedidoId}, Status: {$novoStatus}");

            $pedido = Pedido::find($pedidoId);

            if (!$pedido) {
                Log::warning("Pedido {$pedidoId} não encontrado no teste");
                $response = json_encode(['success' => false, 'message' => 'Pedido não encontrado']);
                $statusCode = 404;
            } else {
                Log::info("Pedido encontrado no teste - ID: {$pedido->id}, Status atual: {$pedido->status}");

                $statusAnterior = $pedido->status;

                if ($novoStatus === 'cancelado') {
                    $this->pedidoService->cancelarPedido($pedido);
                    Log::info("Pedido {$pedido->id} cancelado via teste webhook");
                    $response = json_encode([
                        'success' => true,
                        'message' => 'Pedido cancelado e removido',
                        'pedido_id' => $pedidoId,
                        'status_anterior' => $statusAnterior,
                        'acao' => 'cancelado'
                    ]);
                } else {
                    $this->pedidoService->atualizarStatus($pedido, $novoStatus);
                    Log::info("Status do pedido {$pedido->id} atualizado para {$novoStatus} via teste webhook");

                    $pedido->refresh();
                    Log::info("Status após atualização no teste: {$pedido->status}");

                    $response = json_encode([
                        'success' => true,
                        'message' => 'Status atualizado com sucesso',
                        'pedido_id' => $pedidoId,
                        'status_anterior' => $statusAnterior,
                        'status_atual' => $pedido->status,
                        'acao' => 'atualizado'
                    ]);
                }
                $statusCode = 200;
            }

            $pedidos = Pedido::orderBy('created_at', 'desc')->take(10)->get();

            return view('webhook.teste', [
                'pedidos' => $pedidos,
                'response' => $response,
                'status_code' => $statusCode
            ]);

        } catch (\Exception $e) {
            Log::error("Erro no teste webhook: " . $e->getMessage());
            Log::error("Stack trace: " . $e->getTraceAsString());

            $pedidos = Pedido::orderBy('created_at', 'desc')->take(10)->get();

            return view('webhook.teste', [
                'pedidos' => $pedidos,
                'response' => json_encode(['success' => false, 'error' => $e->getMessage()]),
                'status_code' => 500
            ]);
        }
    }
}
