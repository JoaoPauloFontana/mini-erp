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

    public function testarWebhook(TesteWebhookRequest $request = null)
    {
        if ($request && $request->isMethod('post')) {
            $response = $this->receberStatus($request);

            return view('webhook.teste', [
                'response' => $response->getContent(),
                'status_code' => $response->getStatusCode()
            ]);
        }

        $pedidos = Pedido::orderBy('created_at', 'desc')->take(10)->get();

        return view('webhook.teste', compact('pedidos'));
    }
}
