@extends('layouts.app')

@section('title', $produto->nome . ' - Mini ERP')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>{{ $produto->nome }}</h1>
            <div class="btn-group">
                <a href="{{ route('produtos.edit', $produto) }}" class="btn btn-outline-primary">
                    <i class="bi bi-pencil"></i> Editar
                </a>
                <a href="{{ route('produtos.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Voltar
                </a>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5>Informações do Produto</h5>
                        <p><strong>Nome:</strong> {{ $produto->nome }}</p>
                        <p><strong>Preço:</strong> R$ {{ number_format($produto->preco, 2, ',', '.') }}</p>
                        <p><strong>Descrição:</strong> {{ $produto->descricao ?: 'Sem descrição' }}</p>
                    </div>
                    <div class="col-md-6">
                        <h5>Estoque Principal</h5>
                        @php
                            $estoquePrincipal = $produto->estoque->where('variacao_id', null)->first();
                            $quantidadePrincipal = $estoquePrincipal ? $estoquePrincipal->quantidade : 0;
                        @endphp
                        <p><strong>Quantidade:</strong> {{ $quantidadePrincipal }} unidades</p>
                        <div class="mt-3">
                            <form class="add-to-cart-form">
                                @csrf
                                <input type="hidden" name="produto_id" value="{{ $produto->id }}">
                                <div class="input-group mb-3">
                                    <input type="number" name="quantidade" class="form-control" value="1" min="1" max="{{ $quantidadePrincipal }}">
                                    <button type="submit" class="btn btn-success" {{ $quantidadePrincipal <= 0 ? 'disabled' : '' }}>
                                        <i class="bi bi-cart-plus"></i> Comprar
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($produto->variacoes->isNotEmpty())
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Variações</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($produto->variacoes as $variacao)
                            @php
                                $estoqueVariacao = $variacao->estoque->first();
                                $quantidadeVariacao = $estoqueVariacao ? $estoqueVariacao->quantidade : 0;
                            @endphp
                            <div class="col-md-6 mb-3">
                                <div class="border rounded p-3">
                                    <h6>{{ $variacao->nome }}</h6>
                                    <p class="mb-1">
                                        <strong>Preço:</strong> 
                                        R$ {{ number_format($produto->preco + $variacao->valor_adicional, 2, ',', '.') }}
                                        @if($variacao->valor_adicional > 0)
                                            <small class="text-muted">(+R$ {{ number_format($variacao->valor_adicional, 2, ',', '.') }})</small>
                                        @endif
                                    </p>
                                    <p class="mb-2">
                                        <strong>Estoque:</strong> {{ $quantidadeVariacao }} unidades
                                    </p>
                                    <form class="add-to-cart-form">
                                        @csrf
                                        <input type="hidden" name="produto_id" value="{{ $produto->id }}">
                                        <input type="hidden" name="variacao_id" value="{{ $variacao->id }}">
                                        <div class="input-group input-group-sm">
                                            <input type="number" name="quantidade" class="form-control" value="1" min="1" max="{{ $quantidadeVariacao }}">
                                            <button type="submit" class="btn btn-success" {{ $quantidadeVariacao <= 0 ? 'disabled' : '' }}>
                                                <i class="bi bi-cart-plus"></i> Comprar
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @else
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Variações</h5>
                </div>
                <div class="card-body text-center text-muted">
                    <i class="bi bi-tags display-4"></i>
                    <p>Nenhuma variação cadastrada</p>
                </div>
            </div>
        @endif
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-info-circle"></i> Resumo</h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled">
                    <li><strong>ID:</strong> {{ $produto->id }}</li>
                    <li><strong>Criado em:</strong> {{ $produto->created_at->format('d/m/Y H:i') }}</li>
                    <li><strong>Atualizado em:</strong> {{ $produto->updated_at->format('d/m/Y H:i') }}</li>
                    <li><strong>Total de variações:</strong> {{ $produto->variacoes->count() }}</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('.add-to-cart-form').on('submit', function(e) {
        e.preventDefault();
        
        var form = $(this);
        var button = form.find('button[type="submit"]');
        var originalText = button.html();
        
        button.prop('disabled', true).html('<i class="spinner-border spinner-border-sm"></i> Adicionando...');
        
        $.post('{{ route("carrinho.adicionar") }}', form.serialize())
            .done(function(response) {
                if (response.success) {
                    // Atualizar badge do carrinho
                    updateCartBadge(response.quantidade_carrinho);
                    
                    // Mostrar mensagem de sucesso
                    showAlert('success', response.message);
                } else {
                    showAlert('danger', response.error);
                }
            })
            .fail(function() {
                showAlert('danger', 'Erro ao adicionar produto ao carrinho');
            })
            .always(function() {
                button.prop('disabled', false).html(originalText);
            });
    });
});
</script>
@endpush
