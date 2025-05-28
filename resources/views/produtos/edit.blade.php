@extends('layouts.app')

@section('title', 'Editar Produto - Mini ERP')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="bi bi-pencil"></i> Editar Produto</h1>
    <div class="btn-group">
        <a href="{{ route('produtos.show', $produto) }}" class="btn btn-outline-primary">
            <i class="bi bi-eye"></i> Ver Produto
        </a>
        <a href="{{ route('produtos.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Voltar
        </a>
    </div>
</div>

<form method="POST" action="{{ route('produtos.update', $produto) }}" id="produto-form">
    @csrf
    @method('PUT')
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
                               id="nome" name="nome" value="{{ old('nome', $produto->nome) }}" required>
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
                                       id="preco" name="preco" value="{{ old('preco', $produto->preco) }}"
                                       step="0.01" min="0" required>
                            </div>
                            @error('preco')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="estoque_principal" class="form-label">Estoque Principal</label>
                            @php
                                $estoquePrincipal = $produto->estoque->where('variacao_id', null)->first();
                                $quantidadePrincipal = $estoquePrincipal ? $estoquePrincipal->quantidade : 0;
                            @endphp
                            <input type="number" class="form-control @error('estoque_principal') is-invalid @enderror"
                                   id="estoque_principal" name="estoque_principal"
                                   value="{{ old('estoque_principal', $quantidadePrincipal) }}" min="0">
                            @error('estoque_principal')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="descricao" class="form-label">Descrição</label>
                        <textarea class="form-control @error('descricao') is-invalid @enderror"
                                  id="descricao" name="descricao" rows="4"
                                  placeholder="Descrição detalhada do produto">{{ old('descricao', $produto->descricao) }}</textarea>
                        @error('descricao')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            @if($produto->variacoes->isNotEmpty())
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Variações do Produto</h5>
                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalNovaVariacao">
                            <i class="bi bi-plus"></i> Nova Variação
                        </button>
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
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <h6 class="mb-0">{{ $variacao->nome }}</h6>
                                            <button type="button" class="btn btn-sm btn-outline-danger"
                                                    onclick="confirmarExclusaoVariacao({{ $variacao->id }}, '{{ $variacao->nome }}')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>

                                        <div class="mb-2">
                                            <label class="form-label small">Nome da Variação</label>
                                            <input type="text" class="form-control form-control-sm"
                                                   name="variacoes[{{ $variacao->id }}][nome]"
                                                   value="{{ $variacao->nome }}">
                                        </div>

                                        <div class="mb-2">
                                            <label class="form-label small">Valor Adicional</label>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text">R$</span>
                                                <input type="number" class="form-control"
                                                       name="variacoes[{{ $variacao->id }}][valor_adicional]"
                                                       value="{{ $variacao->valor_adicional }}" step="0.01">
                                            </div>
                                        </div>

                                        <div class="mb-2">
                                            <label class="form-label small">Estoque</label>
                                            <input type="number" class="form-control form-control-sm"
                                                   name="variacoes[{{ $variacao->id }}][estoque]"
                                                   value="{{ $quantidadeVariacao }}" min="0">
                                        </div>

                                        <div class="text-muted small">
                                            Preço final: R$ {{ number_format($produto->preco + $variacao->valor_adicional, 2, ',', '.') }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @else
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Variações do Produto</h5>
                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalNovaVariacao">
                            <i class="bi bi-plus"></i> Nova Variação
                        </button>
                    </div>
                    <div class="card-body text-center text-muted">
                        <i class="bi bi-tags display-4"></i>
                        <p>Nenhuma variação cadastrada</p>
                        <small>Clique em "Nova Variação" para adicionar opções do produto</small>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Ações</h5>
                </div>
                <div class="card-body">
                    <button type="submit" class="btn btn-success w-100 mb-2">
                        <i class="bi bi-check-circle"></i> Salvar Alterações
                    </button>
                    <a href="{{ route('produtos.show', $produto) }}" class="btn btn-outline-secondary w-100 mb-2">
                        <i class="bi bi-x-circle"></i> Cancelar
                    </a>
                    <hr>
                    <button type="button" class="btn btn-outline-danger w-100"
                            onclick="confirmarExclusao({{ $produto->id }}, '{{ $produto->nome }}')">
                        <i class="bi bi-trash"></i> Excluir Produto
                    </button>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-info-circle"></i> Informações</h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled small">
                        <li><strong>ID:</strong> {{ $produto->id }}</li>
                        <li><strong>Criado:</strong> {{ $produto->created_at->format('d/m/Y H:i') }}</li>
                        <li><strong>Atualizado:</strong> {{ $produto->updated_at->format('d/m/Y H:i') }}</li>
                        <li><strong>Variações:</strong> {{ $produto->variacoes->count() }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Modal Nova Variação -->
<div class="modal fade" id="modalNovaVariacao" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" action="{{ route('produtos.adicionar-variacao', $produto) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Nova Variação</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="variacao_nome" class="form-label">Nome da Variação</label>
                        <input type="text" class="form-control" id="variacao_nome" name="variacao_nome" required>
                        <div class="form-text">Ex: P, M, G, 36, 38, Azul, Vermelho</div>
                    </div>
                    <div class="mb-3">
                        <label for="variacao_valor_adicional" class="form-label">Valor Adicional</label>
                        <input type="number" class="form-control" id="variacao_valor_adicional"
                               name="variacao_valor_adicional" step="0.01" value="0">
                        <div class="form-text">Valor a ser adicionado ao preço base</div>
                    </div>
                    <div class="mb-3">
                        <label for="variacao_estoque" class="form-label">Estoque Inicial</label>
                        <input type="number" class="form-control" id="variacao_estoque"
                               name="variacao_estoque" min="0" value="0">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Adicionar Variação</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de confirmação de exclusão do produto -->
<div class="modal fade" id="modalExclusao" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Exclusão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja excluir o produto <strong id="nomeProduto"></strong>?</p>
                <p class="text-muted">Esta ação não pode ser desfeita.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="formExclusao" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash"></i> Excluir
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmarExclusao(id, nome) {
    document.getElementById('nomeProduto').textContent = nome;
    document.getElementById('formExclusao').action = '/produtos/' + id;

    var modal = new bootstrap.Modal(document.getElementById('modalExclusao'));
    modal.show();
}

function confirmarExclusaoVariacao(id, nome) {
    if (confirm('Tem certeza que deseja excluir a variação "' + nome + '"?')) {
        alert('Funcionalidade de exclusão de variação será implementada');
    }
}
</script>
@endpush
