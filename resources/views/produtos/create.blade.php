@extends('layouts.app')

@section('title', 'Novo Produto - Mini ERP')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="bi bi-plus-circle"></i> Novo Produto</h1>
    <a href="{{ route('produtos.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Voltar
    </a>
</div>

<form method="POST" action="{{ route('produtos.store') }}" id="produto-form">
    @csrf
    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Informações Básicas</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome do Produto *</label>
                        <input type="text" class="form-control @error('nome') is-invalid @enderror" 
                               id="nome" name="nome" value="{{ old('nome') }}" required>
                        @error('nome')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="preco" class="form-label">Preço *</label>
                            <div class="input-group">
                                <span class="input-group-text">R$</span>
                                <input type="number" class="form-control @error('preco') is-invalid @enderror" 
                                       id="preco" name="preco" value="{{ old('preco') }}" 
                                       step="0.01" min="0" required>
                            </div>
                            @error('preco')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="estoque_inicial" class="form-label">Estoque Inicial</label>
                            <input type="number" class="form-control @error('estoque_inicial') is-invalid @enderror" 
                                   id="estoque_inicial" name="estoque_inicial" 
                                   value="{{ old('estoque_inicial', 0) }}" min="0">
                            @error('estoque_inicial')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="descricao" class="form-label">Descrição</label>
                        <textarea class="form-control @error('descricao') is-invalid @enderror" 
                                  id="descricao" name="descricao" rows="4" 
                                  placeholder="Descrição detalhada do produto">{{ old('descricao') }}</textarea>
                        @error('descricao')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Variações do Produto</h5>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="btn-add-variacao">
                        <i class="bi bi-plus"></i> Adicionar Variação
                    </button>
                </div>
                <div class="card-body">
                    <p class="text-muted">
                        As variações permitem criar diferentes opções do mesmo produto (ex: tamanhos, cores, etc.)
                    </p>
                    
                    <div id="variacoes-container">
                        <!-- Variações serão adicionadas aqui via JavaScript -->
                    </div>
                    
                    <div id="no-variacoes" class="text-center text-muted py-3">
                        <i class="bi bi-tags display-4"></i>
                        <p>Nenhuma variação adicionada</p>
                        <small>Clique em "Adicionar Variação" para criar opções do produto</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Ações</h5>
                </div>
                <div class="card-body">
                    <button type="submit" class="btn btn-success w-100 mb-2">
                        <i class="bi bi-check-circle"></i> Salvar Produto
                    </button>
                    <a href="{{ route('produtos.index') }}" class="btn btn-outline-secondary w-100">
                        <i class="bi bi-x-circle"></i> Cancelar
                    </a>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-info-circle"></i> Dicas</h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled small">
                        <li><i class="bi bi-check text-success"></i> Use nomes descritivos</li>
                        <li><i class="bi bi-check text-success"></i> Defina preços competitivos</li>
                        <li><i class="bi bi-check text-success"></i> Adicione descrições detalhadas</li>
                        <li><i class="bi bi-check text-success"></i> Configure o estoque inicial</li>
                        <li><i class="bi bi-check text-success"></i> Use variações para opções</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Template para variação -->
<template id="variacao-template">
    <div class="variacao-item border rounded p-3 mb-3">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="mb-0">Variação</h6>
            <button type="button" class="btn btn-sm btn-outline-danger btn-remove-variacao">
                <i class="bi bi-trash"></i>
            </button>
        </div>
        <div class="row">
            <div class="col-md-6 mb-2">
                <label class="form-label">Nome da Variação</label>
                <input type="text" class="form-control" name="variacoes[INDEX][nome]" 
                       placeholder="Ex: P, M, G, Azul, 36">
            </div>
            <div class="col-md-6 mb-2">
                <label class="form-label">Valor Adicional</label>
                <div class="input-group">
                    <span class="input-group-text">R$</span>
                    <input type="number" class="form-control" name="variacoes[INDEX][valor_adicional]" 
                           step="0.01" value="0">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-2">
                <label class="form-label">Estoque</label>
                <input type="number" class="form-control" name="variacoes[INDEX][estoque]" 
                       min="0" value="0">
            </div>
        </div>
    </div>
</template>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    var variacaoIndex = 0;

    $('#btn-add-variacao').on('click', function() {
        adicionarVariacao();
    });

    $(document).on('click', '.btn-remove-variacao', function() {
        $(this).closest('.variacao-item').remove();
        atualizarVisibilidadeVariacoes();
    });

    function adicionarVariacao() {
        var template = $('#variacao-template').html();
        var html = template.replace(/INDEX/g, variacaoIndex);
        
        $('#variacoes-container').append(html);
        variacaoIndex++;
        
        atualizarVisibilidadeVariacoes();
    }

    function atualizarVisibilidadeVariacoes() {
        var hasVariacoes = $('#variacoes-container .variacao-item').length > 0;
        
        if (hasVariacoes) {
            $('#no-variacoes').hide();
        } else {
            $('#no-variacoes').show();
        }
    }

    atualizarVisibilidadeVariacoes();
});
</script>
@endpush
