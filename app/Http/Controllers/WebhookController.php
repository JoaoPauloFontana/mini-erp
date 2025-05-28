<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\Estoque;
use App\Http\Requests\WebhookStatusRequest;
use App\Http\Requests\TesteWebhookRequest;
use App\Http\Resources\WebhookResource;
use App\Http\Resources\TesteWebhookResource;
use App\Services\PedidoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Exception;
use App\Enums\StatusPedido;
use App\Constants\SystemConstants;

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
            return WebhookResource::error(SystemConstants::MSG_PEDIDO_NAO_ENCONTRADO)
                ->response()
                ->setStatusCode(SystemConstants::HTTP_NOT_FOUND);
        }

        try {
            $resultado = $this->processarWebhook($pedido, $request->status);

            return WebhookResource::success([
                'pedido_id' => $request->pedido_id,
                'status' => $request->status,
                'status_anterior' => $resultado['status_anterior'],
                'acao_executada' => $resultado['acao_executada']
            ], $request->status === StatusPedido::CANCELADO->value ?
                SystemConstants::MSG_PEDIDO_CANCELADO :
                SystemConstants::MSG_STATUS_ATUALIZADO);

        } catch (Exception $e) {
            Log::error("Erro no webhook: " . $e->getMessage());
            return WebhookResource::error(SystemConstants::MSG_ERRO_INTERNO)
                ->response()
                ->setStatusCode(SystemConstants::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function testarWebhookForm()
    {
        $pedidos = Pedido::orderBy('created_at', 'desc')->take(SystemConstants::PEDIDOS_RECENTES_LIMITE)->get();
        return view('webhook.teste', compact('pedidos'));
    }

    public function testarWebhookPost(TesteWebhookRequest $request)
    {
        try {
            $pedido = Pedido::find($request->pedido_id);

            if (!$pedido) {
                $resource = TesteWebhookResource::error(SystemConstants::MSG_PEDIDO_NAO_ENCONTRADO);
                $statusCode = SystemConstants::HTTP_NOT_FOUND;
            } else {
                $resultado = $this->processarWebhook($pedido, $request->status);

                $resource = TesteWebhookResource::success([
                    'pedido_id' => $request->pedido_id,
                    'status' => $request->status,
                    'status_anterior' => $resultado['status_anterior'],
                    'acao_executada' => $resultado['acao_executada']
                ], $request->status === StatusPedido::CANCELADO->value ?
                    SystemConstants::MSG_PEDIDO_CANCELADO :
                    SystemConstants::MSG_STATUS_ATUALIZADO);

                $statusCode = SystemConstants::HTTP_OK;
            }

            $pedidos = Pedido::orderBy('created_at', 'desc')->take(SystemConstants::PEDIDOS_RECENTES_LIMITE)->get();

            return view('webhook.teste', [
                'pedidos' => $pedidos,
                'response' => $resource->toJson(),
                'status_code' => $statusCode
            ]);

        } catch (Exception $e) {
            Log::error("Erro no teste webhook: " . $e->getMessage());

            $pedidos = Pedido::orderBy('created_at', 'desc')->take(SystemConstants::PEDIDOS_RECENTES_LIMITE)->get();

            return view('webhook.teste', [
                'pedidos' => $pedidos,
                'response' => TesteWebhookResource::error(SystemConstants::MSG_ERRO_INTERNO, ['error' => $e->getMessage()])->toJson(),
                'status_code' => SystemConstants::HTTP_INTERNAL_SERVER_ERROR
            ]);
        }
    }

    private function processarWebhook(Pedido $pedido, string $novoStatus): array
    {
        $statusAnterior = $pedido->status;
        $acaoExecutada = null;

        if ($novoStatus === StatusPedido::CANCELADO->value) {
            $this->pedidoService->cancelarPedido($pedido);
            $acaoExecutada = SystemConstants::WEBHOOK_ACAO_PEDIDO_REMOVIDO;
        } else {
            $this->pedidoService->atualizarStatus($pedido, $novoStatus);
            $acaoExecutada = SystemConstants::WEBHOOK_ACAO_STATUS_ATUALIZADO;
        }

        return [
            'status_anterior' => $statusAnterior,
            'acao_executada' => $acaoExecutada
        ];
    }
}
