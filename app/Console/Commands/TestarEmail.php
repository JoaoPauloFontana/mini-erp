<?php

namespace App\Console\Commands;

use App\Models\Pedido;
use App\Services\EmailService;
use Illuminate\Console\Command;
use Exception;

class TestarEmail extends Command
{
    protected $signature = 'email:testar
                            {pedido_id? : ID do pedido para enviar e-mail de teste}
                            {--email= : E-mail de destino para teste}';

    protected $description = 'Testar envio de e-mail de confirmação de pedido';

    protected EmailService $emailService;

    public function __construct(EmailService $emailService)
    {
        parent::__construct();
        $this->emailService = $emailService;
    }

    public function handle(): int
    {
        $this->info('🚀 Iniciando teste de envio de e-mail...');

        if (!$this->emailService->verificarConfiguracao()) {
            $this->error('❌ Configuração de e-mail não encontrada ou incompleta.');
            $this->info('💡 Verifique as configurações no arquivo .env:');
            $this->info('   - MAIL_MAILER');
            $this->info('   - MAIL_HOST');
            $this->info('   - MAIL_PORT');
            $this->info('   - MAIL_USERNAME');
            $this->info('   - MAIL_PASSWORD');
            return 1;
        }

        $this->info('✅ Configuração de e-mail verificada.');

        $pedidoId = $this->argument('pedido_id');
        $emailDestino = $this->option('email');

        if ($pedidoId) {
            $pedido = Pedido::with(['itens.produto', 'itens.variacao'])->find($pedidoId);

            if (!$pedido) {
                $this->error("❌ Pedido #{$pedidoId} não encontrado.");
                return 1;
            }
        } else {
            $pedido = Pedido::with(['itens.produto', 'itens.variacao'])
                ->orderBy('created_at', 'desc')
                ->first();

            if (!$pedido) {
                $this->error('❌ Nenhum pedido encontrado no sistema.');
                $this->info('💡 Crie um pedido primeiro ou especifique um ID de pedido válido.');
                return 1;
            }
        }

        $this->info("📦 Usando pedido #{$pedido->id} para teste.");

        $emailOriginal = $pedido->cliente_email;
        if ($emailDestino) {
            $pedido->cliente_email = $emailDestino;
            $this->info("📧 E-mail de destino alterado para: {$emailDestino}");
        }

        try {
            $this->info('📤 Enviando e-mail de teste...');

            $sucesso = $this->emailService->enviarConfirmacaoPedido($pedido);

            if ($sucesso) {
                $this->info('✅ E-mail enviado com sucesso!');
                $this->info("📧 Destinatário: {$pedido->cliente_email}");
                $this->info("📦 Pedido: #{$pedido->id}");
                $this->info("👤 Cliente: {$pedido->cliente_nome}");
                $this->info("💰 Total: R$ " . number_format($pedido->total, 2, ',', '.'));

                if ($emailDestino) {
                    $pedido->cliente_email = $emailOriginal;
                }

                return 0;
            } else {
                $this->error('❌ Falha ao enviar e-mail.');
                return 1;
            }

        } catch (Exception $e) {
            $this->error('❌ Erro ao enviar e-mail: ' . $e->getMessage());
            $this->info('💡 Verifique os logs para mais detalhes.');
            return 1;
        }
    }
}
