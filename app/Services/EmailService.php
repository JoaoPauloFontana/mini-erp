<?php

namespace App\Services;

use App\Models\Pedido;
use App\Mail\PedidoConfirmado;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Exception;

class EmailService
{
    public function enviarConfirmacaoPedido(Pedido $pedido): bool
    {
        try {
            if (empty($pedido->cliente_email)) {
                Log::warning('Tentativa de envio de e-mail para pedido sem e-mail do cliente', [
                    'pedido_id' => $pedido->id
                ]);
                return false;
            }

            $pedido->load(['itens.produto', 'itens.variacao']);

            Mail::to($pedido->cliente_email)
                ->send(new PedidoConfirmado($pedido));

            Log::info('E-mail de confirmação de pedido enviado com sucesso', [
                'pedido_id' => $pedido->id,
                'cliente_email' => $pedido->cliente_email,
                'cliente_nome' => $pedido->cliente_nome
            ]);

            return true;

        } catch (Exception $e) {
            Log::error('Erro ao enviar e-mail de confirmação de pedido', [
                'pedido_id' => $pedido->id,
                'cliente_email' => $pedido->cliente_email ?? 'N/A',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw $e;
        }
    }

    public function enviarAtualizacaoStatus(Pedido $pedido, string $statusAnterior, string $novoStatus): bool
    {
        try {
            if (empty($pedido->cliente_email)) {
                Log::warning('Tentativa de envio de e-mail de status para pedido sem e-mail do cliente', [
                    'pedido_id' => $pedido->id
                ]);
                return false;
            }

            Log::info('Atualização de status de pedido', [
                'pedido_id' => $pedido->id,
                'cliente_email' => $pedido->cliente_email,
                'status_anterior' => $statusAnterior,
                'novo_status' => $novoStatus
            ]);

            return true;

        } catch (Exception $e) {
            Log::error('Erro ao enviar e-mail de atualização de status', [
                'pedido_id' => $pedido->id,
                'cliente_email' => $pedido->cliente_email ?? 'N/A',
                'status_anterior' => $statusAnterior,
                'novo_status' => $novoStatus,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    public function verificarConfiguracao(): bool
    {
        try {
            $driver = config('mail.default');
            $host = config('mail.mailers.' . $driver . '.host');

            return !empty($driver) && !empty($host);

        } catch (Exception $e) {
            Log::error('Erro ao verificar configuração de e-mail', [
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }
}
