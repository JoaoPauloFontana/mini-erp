<?php

namespace App\Mail;

use App\Models\Pedido;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PedidoConfirmado extends Mailable
{
    use Queueable, SerializesModels;

    public Pedido $pedido;

    public function __construct(Pedido $pedido)
    {
        $this->pedido = $pedido;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Confirmação do Pedido #' . $this->pedido->id . ' - Sistema ERP',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.pedido-confirmado',
            with: [
                'pedido' => $this->pedido,
                'itens' => $this->pedido->itens()->with(['produto', 'variacao'])->get(),
                'cliente' => [
                    'nome' => $this->pedido->cliente_nome,
                    'email' => $this->pedido->cliente_email,
                    'telefone' => $this->pedido->cliente_telefone,
                ],
                'endereco' => [
                    'cep' => $this->pedido->cep,
                    'endereco_completo' => $this->pedido->endereco,
                ],
                'totais' => [
                    'subtotal' => $this->pedido->subtotal,
                    'desconto' => $this->pedido->desconto,
                    'frete' => $this->pedido->frete,
                    'total' => $this->pedido->total,
                ]
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
