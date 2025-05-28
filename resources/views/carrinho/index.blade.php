@extends('layouts.app')

@section('title', 'Carrinho - Mini ERP')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="bi bi-cart3"></i> Carrinho de Compras</h1>
    <a href="{{ route('produtos.index') }}" class="btn btn-outline-primary">
        <i class="bi bi-arrow-left"></i> Continuar Comprando
    </a>
</div>

@if(empty($carrinho))
    <div class="text-center py-5">
        <i class="bi bi-cart-x display-1 text-muted"></i>
        <h3 class="text-muted">Seu carrinho está vazio</h3>
        <p class="text-muted">Adicione produtos para continuar</p>
        <a href="{{ route('produtos.index') }}" class="btn btn-primary">
            <i class="bi bi-box"></i> Ver Produtos
        </a>
    </div>
@else
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Itens do Carrinho</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Produto</th>
                                    <th>Preço Unit.</th>
                                    <th>Quantidade</th>
                                    <th>Subtotal</th>
                                    <th width="50"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($carrinho as $chave => $item)
                                    <tr data-chave="{{ $chave }}">
                                        <td>
                                            <strong>{{ $item['nome'] }}</strong>
                                            @if($item['variacao_nome'])
                                                <br><small class="text-muted">{{ $item['variacao_nome'] }}</small>
                                            @endif
                                        </td>
                                        <td>R$ {{ number_format($item['preco_unitario'], 2, ',', '.') }}</td>
                                        <td>
                                            <div class="input-group input-group-sm" style="width: 120px;">
                                                <button class="btn btn-outline-secondary btn-quantidade" type="button" data-acao="diminuir">-</button>
                                                <input type="number" class="form-control text-center quantidade-input" 
                                                       value="{{ $item['quantidade'] }}" min="1" data-chave="{{ $chave }}">
                                                <button class="btn btn-outline-secondary btn-quantidade" type="button" data-acao="aumentar">+</button>
                                            </div>
                                        </td>
                                        <td class="subtotal-item">R$ {{ number_format($item['subtotal'], 2, ',', '.') }}</td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline-danger btn-remover" data-chave="{{ $chave }}">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Resumo do Pedido</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal:</span>
                        <span id="subtotal">R$ {{ number_format($totais['subtotal'], 2, ',', '.') }}</span>
                    </div>
                    
                    @if($totais['desconto'] > 0)
                        <div class="d-flex justify-content-between mb-2 text-success">
                            <span>Desconto:</span>
                            <span id="desconto">-R$ {{ number_format($totais['desconto'], 2, ',', '.') }}</span>
                        </div>
                    @endif
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>Frete:</span>
                        <span id="frete">
                            @if($totais['frete'] == 0)
                                <span class="text-success">Grátis</span>
                            @else
                                R$ {{ number_format($totais['frete'], 2, ',', '.') }}
                            @endif
                        </span>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between mb-3">
                        <strong>Total:</strong>
                        <strong id="total">R$ {{ number_format($totais['total'], 2, ',', '.') }}</strong>
                    </div>

                    <!-- Cupom de Desconto -->
                    <div class="mb-3">
                        @if($cupom)
                            <div class="alert alert-success d-flex justify-content-between align-items-center">
                                <span>
                                    <i class="bi bi-tag"></i> {{ $cupom['codigo'] }}
                                </span>
                                <button type="button" class="btn btn-sm btn-outline-danger" id="btn-remover-cupom">
                                    <i class="bi bi-x"></i>
                                </button>
                            </div>
                        @else
                            <div class="input-group">
                                <input type="text" class="form-control" id="codigo-cupom" placeholder="Código do cupom">
                                <button class="btn btn-outline-secondary" type="button" id="btn-aplicar-cupom">
                                    Aplicar
                                </button>
                            </div>
                        @endif
                    </div>

                    <a href="{{ route('carrinho.checkout') }}" class="btn btn-success w-100">
                        <i class="bi bi-credit-card"></i> Finalizar Compra
                    </a>
                </div>
            </div>

            <!-- Informações de Frete -->
            <div class="card mt-3">
                <div class="card-body">
                    <h6><i class="bi bi-truck"></i> Informações de Frete</h6>
                    <small class="text-muted">
                        • Frete grátis para compras acima de R$ 200,00<br>
                        • Frete promocional de R$ 15,00 para compras entre R$ 52,00 e R$ 166,59<br>
                        • Frete normal: R$ 20,00
                    </small>
                </div>
            </div>
        </div>
    </div>
@endif
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('.btn-quantidade').on('click', function() {
        var btn = $(this);
        var acao = btn.data('acao');
        var row = btn.closest('tr');
        var input = row.find('.quantidade-input');
        var quantidade = parseInt(input.val());
        
        if (acao === 'aumentar') {
            quantidade++;
        } else if (acao === 'diminuir' && quantidade > 1) {
            quantidade--;
        }
        
        input.val(quantidade);
        atualizarQuantidade(input.data('chave'), quantidade);
    });

    $('.quantidade-input').on('change', function() {
        var input = $(this);
        var quantidade = parseInt(input.val());
        
        if (quantidade < 1) {
            quantidade = 1;
            input.val(quantidade);
        }
        
        atualizarQuantidade(input.data('chave'), quantidade);
    });

    $('.btn-remover').on('click', function() {
        var chave = $(this).data('chave');
        removerItem(chave);
    });

    $('#btn-aplicar-cupom').on('click', function() {
        var codigo = $('#codigo-cupom').val().trim();
        if (codigo) {
            aplicarCupom(codigo);
        }
    });

    $('#btn-remover-cupom').on('click', function() {
        removerCupom();
    });
});

function atualizarQuantidade(chave, quantidade) {
    $.post('{{ route("carrinho.atualizar") }}', {
        chave: chave,
        quantidade: quantidade
    })
    .done(function(response) {
        if (response.success) {
            atualizarTotais(response.totais);
            updateCartBadge(response.quantidade_carrinho);
        } else {
            showAlert('danger', response.error);
        }
    })
    .fail(function() {
        showAlert('danger', 'Erro ao atualizar quantidade');
    });
}

function removerItem(chave) {
    $.post('{{ route("carrinho.remover") }}', {
        chave: chave
    })
    .done(function(response) {
        if (response.success) {
            $('tr[data-chave="' + chave + '"]').remove();
            atualizarTotais(response.totais);
            updateCartBadge(response.quantidade_carrinho);
            verificarCarrinhoVazio();
            showAlert('success', response.message);
        } else {
            showAlert('danger', response.error);
        }
    })
    .fail(function() {
        showAlert('danger', 'Erro ao remover item');
    });
}

function atualizarTotais(totais) {
    $('#subtotal').text('R$ ' + formatarMoeda(totais.subtotal));
    $('#desconto').text('-R$ ' + formatarMoeda(totais.desconto));
    $('#frete').html(totais.frete === 0 ? '<span class="text-success">Grátis</span>' : 'R$ ' + formatarMoeda(totais.frete));
    $('#total').text('R$ ' + formatarMoeda(totais.total));
}

function verificarCarrinhoVazio() {
    if ($('tbody tr').length === 0) {
        location.reload();
    }
}

function formatarMoeda(valor) {
    return parseFloat(valor).toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}
</script>
@endpush
