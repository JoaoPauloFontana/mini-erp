<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmação do Pedido #{{ $pedido->id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #007bff;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #007bff;
            margin: 0;
        }
        .section {
            margin-bottom: 25px;
        }
        .section h2 {
            color: #007bff;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }
        .info-item {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
        }
        .info-item strong {
            color: #007bff;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .items-table th,
        .items-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .items-table th {
            background-color: #007bff;
            color: white;
        }
        .items-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .totals {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin-top: 20px;
        }
        .totals .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .totals .total-final {
            font-weight: bold;
            font-size: 1.2em;
            color: #007bff;
            border-top: 2px solid #007bff;
            padding-top: 10px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            color: #666;
        }
        @media (max-width: 600px) {
            .info-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Pedido Confirmado!</h1>
            <p>Obrigado por sua compra. Seu pedido foi recebido e está sendo processado.</p>
        </div>

        <div class="section">
            <h2>Informações do Pedido</h2>
            <div class="info-grid">
                <div class="info-item">
                    <strong>Número do Pedido:</strong><br>
                    #{{ $pedido->id }}
                </div>
                <div class="info-item">
                    <strong>Data do Pedido:</strong><br>
                    {{ $pedido->created_at->format('d/m/Y H:i') }}
                </div>
                <div class="info-item">
                    <strong>Status:</strong><br>
                    {{ ucfirst($pedido->status) }}
                </div>
                <div class="info-item">
                    <strong>Forma de Pagamento:</strong><br>
                    {{ $pedido->forma_pagamento ?? 'Não informado' }}
                </div>
            </div>
        </div>

        <div class="section">
            <h2>Dados do Cliente</h2>
            <div class="info-grid">
                <div class="info-item">
                    <strong>Nome:</strong><br>
                    {{ $cliente['nome'] }}
                </div>
                <div class="info-item">
                    <strong>E-mail:</strong><br>
                    {{ $cliente['email'] }}
                </div>
                @if($cliente['telefone'])
                <div class="info-item">
                    <strong>Telefone:</strong><br>
                    {{ $cliente['telefone'] }}
                </div>
                @endif
            </div>
        </div>

        <div class="section">
            <h2>Endereço de Entrega</h2>
            <div class="info-item">
                <strong>CEP:</strong> {{ $endereco['cep'] }}<br>
                <strong>Endereço:</strong> {{ $endereco['endereco_completo'] }}
            </div>
        </div>

        <div class="section">
            <h2>Itens do Pedido</h2>
            <table class="items-table">
                <thead>
                    <tr>
                        <th>Produto</th>
                        <th>Quantidade</th>
                        <th>Preço Unit.</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($itens as $item)
                    <tr>
                        <td>
                            {{ $item->produto->nome }}
                            @if($item->variacao)
                                <br><small>{{ $item->variacao->nome }}</small>
                            @endif
                        </td>
                        <td>{{ $item->quantidade }}</td>
                        <td>R$ {{ number_format($item->preco_unitario, 2, ',', '.') }}</td>
                        <td>R$ {{ number_format($item->subtotal, 2, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="totals">
            <div class="total-row">
                <span>Subtotal:</span>
                <span>R$ {{ number_format($totais['subtotal'], 2, ',', '.') }}</span>
            </div>
            @if($totais['desconto'] > 0)
            <div class="total-row">
                <span>Desconto:</span>
                <span>- R$ {{ number_format($totais['desconto'], 2, ',', '.') }}</span>
            </div>
            @endif
            <div class="total-row">
                <span>Frete:</span>
                <span>
                    @if($totais['frete'] > 0)
                        R$ {{ number_format($totais['frete'], 2, ',', '.') }}
                    @else
                        Grátis
                    @endif
                </span>
            </div>
            <div class="total-row total-final">
                <span>Total:</span>
                <span>R$ {{ number_format($totais['total'], 2, ',', '.') }}</span>
            </div>
        </div>

        <div class="footer">
            <p>Este é um e-mail automático, não responda.</p>
            <p>Em caso de dúvidas, entre em contato conosco.</p>
            <p><strong>Sistema ERP</strong> - Obrigado pela preferência!</p>
        </div>
    </div>
</body>
</html>
