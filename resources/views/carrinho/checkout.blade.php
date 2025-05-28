@extends('layouts.app')

@section('title', 'Finalizar Compra - Mini ERP')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <h1><i class="bi bi-credit-card"></i> Finalizar Compra</h1>
        
        <form method="POST" action="{{ route('carrinho.finalizar') }}" id="checkout-form">
            @csrf
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-person"></i> Dados do Cliente</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="cliente_nome" class="form-label">Nome Completo *</label>
                            <input type="text" class="form-control @error('cliente_nome') is-invalid @enderror" 
                                   id="cliente_nome" name="cliente_nome" value="{{ old('cliente_nome') }}" required>
                            @error('cliente_nome')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="cliente_email" class="form-label">E-mail *</label>
                            <input type="email" class="form-control @error('cliente_email') is-invalid @enderror" 
                                   id="cliente_email" name="cliente_email" value="{{ old('cliente_email') }}" required>
                            @error('cliente_email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="cliente_telefone" class="form-label">Telefone</label>
                            <input type="tel" class="form-control @error('cliente_telefone') is-invalid @enderror" 
                                   id="cliente_telefone" name="cliente_telefone" value="{{ old('cliente_telefone') }}">
                            @error('cliente_telefone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-geo-alt"></i> Endereço de Entrega</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="cep" class="form-label">CEP *</label>
                            <div class="input-group">
                                <input type="text" class="form-control @error('cep') is-invalid @enderror" 
                                       id="cep" name="cep" value="{{ old('cep') }}" 
                                       placeholder="00000-000" maxlength="9" required>
                                <button type="button" class="btn btn-outline-secondary" id="btn-buscar-cep">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                            @error('cep')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label for="logradouro" class="form-label">Logradouro</label>
                            <input type="text" class="form-control" id="logradouro" readonly>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="numero" class="form-label">Número *</label>
                            <input type="text" class="form-control" id="numero" name="numero" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="complemento" class="form-label">Complemento</label>
                            <input type="text" class="form-control" id="complemento" name="complemento">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="bairro" class="form-label">Bairro</label>
                            <input type="text" class="form-control" id="bairro" readonly>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="cidade" class="form-label">Cidade</label>
                            <input type="text" class="form-control" id="cidade" readonly>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="endereco" class="form-label">Endereço Completo *</label>
                        <textarea class="form-control @error('endereco') is-invalid @enderror" 
                                  id="endereco" name="endereco" rows="3" 
                                  placeholder="Endereço completo para entrega" required>{{ old('endereco') }}</textarea>
                        @error('endereco')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between">
                <a href="{{ route('carrinho.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Voltar ao Carrinho
                </a>
                <button type="submit" class="btn btn-success btn-lg">
                    <i class="bi bi-check-circle"></i> Finalizar Pedido
                </button>
            </div>
        </form>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Resumo do Pedido</h5>
            </div>
            <div class="card-body">
                <!-- Itens do carrinho -->
                <div class="mb-3">
                    <h6>Itens ({{ count($carrinho) }})</h6>
                    @foreach($carrinho as $item)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <small>{{ $item['nome'] }}</small>
                                @if($item['variacao_nome'])
                                    <br><small class="text-muted">{{ $item['variacao_nome'] }}</small>
                                @endif
                                <br><small class="text-muted">Qtd: {{ $item['quantidade'] }}</small>
                            </div>
                            <small>R$ {{ number_format($item['subtotal'], 2, ',', '.') }}</small>
                        </div>
                    @endforeach
                </div>

                <hr>

                <!-- Totais -->
                <div class="d-flex justify-content-between mb-2">
                    <span>Subtotal:</span>
                    <span>R$ {{ number_format($totais['subtotal'], 2, ',', '.') }}</span>
                </div>
                
                @if($totais['desconto'] > 0)
                    <div class="d-flex justify-content-between mb-2 text-success">
                        <span>Desconto:</span>
                        <span>-R$ {{ number_format($totais['desconto'], 2, ',', '.') }}</span>
                    </div>
                    @if($cupom)
                        <div class="mb-2">
                            <small class="text-muted">
                                <i class="bi bi-tag"></i> {{ $cupom['codigo'] }}
                            </small>
                        </div>
                    @endif
                @endif
                
                <div class="d-flex justify-content-between mb-2">
                    <span>Frete:</span>
                    <span>
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
                    <strong class="text-success">R$ {{ number_format($totais['total'], 2, ',', '.') }}</strong>
                </div>

                <!-- Informações de segurança -->
                <div class="alert alert-info">
                    <small>
                        <i class="bi bi-shield-check"></i>
                        Seus dados estão seguros. Após a confirmação, você receberá um e-mail com os detalhes do pedido.
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#cep').on('input', function() {
        var value = $(this).val().replace(/\D/g, '');
        value = value.replace(/^(\d{5})(\d)/, '$1-$2');
        $(this).val(value);
    });

    $('#btn-buscar-cep, #cep').on('click keypress', function(e) {
        if (e.type === 'click' || e.which === 13) {
            e.preventDefault();
            buscarCEP();
        }
    });

    $('#logradouro, #numero, #complemento, #bairro, #cidade').on('input', function() {
        atualizarEnderecoCompleto();
    });
});

function buscarCEP() {
    var cep = $('#cep').val().replace(/\D/g, '');
    
    if (cep.length !== 8) {
        showAlert('warning', 'CEP deve ter 8 dígitos');
        return;
    }

    var btn = $('#btn-buscar-cep');
    var originalText = btn.html();
    btn.prop('disabled', true).html('<i class="spinner-border spinner-border-sm"></i>');

    $.post('{{ route("carrinho.cep") }}', { cep: cep })
        .done(function(response) {
            if (response.success && response.data) {
                var dados = response.data;
                $('#logradouro').val(dados.logradouro || '');
                $('#bairro').val(dados.bairro || '');
                $('#cidade').val(dados.localidade || '');
                $('#numero').focus();
                atualizarEnderecoCompleto();
                showAlert('success', 'CEP encontrado!');
            } else {
                showAlert('danger', response.error || 'CEP não encontrado');
            }
        })
        .fail(function() {
            showAlert('danger', 'Erro ao consultar CEP');
        })
        .always(function() {
            btn.prop('disabled', false).html(originalText);
        });
}

function atualizarEnderecoCompleto() {
    var endereco = '';
    var logradouro = $('#logradouro').val();
    var numero = $('#numero').val();
    var complemento = $('#complemento').val();
    var bairro = $('#bairro').val();
    var cidade = $('#cidade').val();

    if (logradouro) {
        endereco += logradouro;
        if (numero) {
            endereco += ', ' + numero;
        }
        if (complemento) {
            endereco += ', ' + complemento;
        }
        if (bairro) {
            endereco += ' - ' + bairro;
        }
        if (cidade) {
            endereco += ' - ' + cidade;
        }
    }

    $('#endereco').val(endereco);
}
</script>
@endpush
