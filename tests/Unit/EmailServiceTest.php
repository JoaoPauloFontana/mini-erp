<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\EmailService;
use App\Models\Pedido;
use App\Models\PedidoItem;
use App\Models\Produto;
use App\Mail\PedidoConfirmado;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

class EmailServiceTest extends TestCase
{
    use RefreshDatabase;

    private EmailService $emailService;

    protected function setUp(): void
    {
        parent::setUp();

        Config::set('mail.default', 'smtp');
        Config::set('mail.mailers.smtp.host', 'localhost');
        Config::set('mail.from.address', 'test@example.com');
        Config::set('mail.from.name', 'Test System');

        $this->emailService = new EmailService();
        Mail::fake();
    }

    public function test_enviar_confirmacao_pedido_com_sucesso()
    {
        $produto = Produto::factory()->create([
            'nome' => 'Produto Teste',
            'preco' => 100.00
        ]);

        $pedido = Pedido::factory()->create([
            'cliente_nome' => 'João Silva',
            'cliente_email' => 'joao@teste.com',
            'cliente_telefone' => '11999999999',
            'cep' => '01234567',
            'endereco' => 'Rua Teste, 123',
            'subtotal' => 100.00,
            'desconto' => 0.00,
            'frete' => 15.00,
            'total' => 115.00,
            'status' => 'pendente'
        ]);

        PedidoItem::factory()->create([
            'pedido_id' => $pedido->id,
            'produto_id' => $produto->id,
            'quantidade' => 1,
            'preco_unitario' => 100.00,
            'subtotal' => 100.00
        ]);

        $resultado = $this->emailService->enviarConfirmacaoPedido($pedido);

        $this->assertTrue($resultado);

        Mail::assertSent(PedidoConfirmado::class, function ($mail) use ($pedido) {
            return $mail->pedido->id === $pedido->id;
        });

        Mail::assertSent(PedidoConfirmado::class, 1);
    }

    public function test_falha_envio_pedido_sem_email()
    {
        $pedido = Pedido::factory()->semEmail()->create();

        $resultado = $this->emailService->enviarConfirmacaoPedido($pedido);

        $this->assertFalse($resultado);
        Mail::assertNothingSent();
    }

    public function test_falha_envio_pedido_email_vazio()
    {
        $pedido = Pedido::factory()->create([
            'cliente_email' => ''
        ]);

        $resultado = $this->emailService->enviarConfirmacaoPedido($pedido);

        $this->assertFalse($resultado);
        Mail::assertNothingSent();
    }

    public function test_verificar_configuracao_email()
    {
        $configurado = $this->emailService->verificarConfiguracao();

        $this->assertIsBool($configurado);
    }

    public function test_enviar_atualizacao_status()
    {
        $pedido = Pedido::factory()->create([
            'cliente_email' => 'teste@exemplo.com',
            'status' => 'pendente'
        ]);

        $resultado = $this->emailService->enviarAtualizacaoStatus(
            $pedido,
            'pendente',
            'confirmado'
        );

        $this->assertTrue($resultado);
    }

    public function test_email_contem_dados_corretos_pedido()
    {
        $produto = Produto::factory()->create([
            'nome' => 'Produto Específico',
            'preco' => 50.00
        ]);

        $pedido = Pedido::factory()->create([
            'cliente_nome' => 'Maria Santos',
            'cliente_email' => 'maria@teste.com',
            'total' => 65.00
        ]);

        PedidoItem::factory()->create([
            'pedido_id' => $pedido->id,
            'produto_id' => $produto->id,
            'quantidade' => 2,
            'preco_unitario' => 50.00,
            'subtotal' => 100.00
        ]);

        $this->emailService->enviarConfirmacaoPedido($pedido);

        Mail::assertSent(PedidoConfirmado::class, function ($mail) use ($pedido) {
            return $mail->pedido->id === $pedido->id &&
                   $mail->pedido->cliente_nome === 'Maria Santos' &&
                   $mail->pedido->cliente_email === 'maria@teste.com';
        });
    }

    public function test_email_enviado_para_endereco_correto()
    {
        $emailCliente = 'cliente@exemplo.com';
        $pedido = Pedido::factory()->create([
            'cliente_email' => $emailCliente
        ]);

        $this->emailService->enviarConfirmacaoPedido($pedido);

        Mail::assertSent(PedidoConfirmado::class, function ($mail) use ($emailCliente) {
            return $mail->hasTo($emailCliente);
        });
    }

    public function test_carregamento_relacionamentos_pedido()
    {
        $produto = Produto::factory()->create();
        $pedido = Pedido::factory()->create([
            'cliente_email' => 'teste@exemplo.com'
        ]);

        PedidoItem::factory()->create([
            'pedido_id' => $pedido->id,
            'produto_id' => $produto->id
        ]);

        $this->emailService->enviarConfirmacaoPedido($pedido);

        $this->assertTrue($pedido->relationLoaded('itens'));
        Mail::assertSent(PedidoConfirmado::class);
    }
}
