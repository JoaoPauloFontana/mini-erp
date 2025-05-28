@extends('layouts.app')

@section('title', 'Produtos - Mini ERP')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="bi bi-box"></i> Produtos</h1>
    <a href="{{ route('produtos.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Novo Produto
    </a>
</div>

@if($produtos->isEmpty())
    <div class="text-center py-5">
        <i class="bi bi-box display-1 text-muted"></i>
        <h3 class="text-muted">Nenhum produto cadastrado</h3>
        <p class="text-muted">Comece criando seu primeiro produto</p>
        <a href="{{ route('produtos.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Criar Produto
        </a>
    </div>
@else
    <div class="row">
        @foreach($produtos as $produto)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">{{ $produto->nome }}</h5>
                        <p class="card-text text-muted">
                            {{ $produto->descricao ?: 'Sem descrição' }}
                        </p>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="h5 text-primary mb-0">
                                R$ {{ number_format($produto->preco, 2, ',', '.') }}
                            </span>
                            @php
                                $estoqueTotal = $produto->estoque->sum('quantidade');
                            @endphp
                            <span class="badge {{ $estoqueTotal > 0 ? 'bg-success' : 'bg-warning' }}">
                                {{ $estoqueTotal > 0 ? 'Em estoque' : 'Sem estoque' }}
                            </span>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent">
                        <div class="btn-group w-100" role="group">
                            <a href="{{ route('produtos.show', $produto) }}" class="btn btn-outline-primary">
                                <i class="bi bi-eye"></i> Ver
                            </a>
                            <a href="{{ route('produtos.edit', $produto) }}" class="btn btn-outline-secondary">
                                <i class="bi bi-pencil"></i> Editar
                            </a>
                            <button type="button" class="btn btn-outline-danger" 
                                    onclick="confirmarExclusao({{ $produto->id }}, '{{ $produto->nome }}')">
                                <i class="bi bi-trash"></i> Excluir
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif

<!-- Modal de confirmação de exclusão -->
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
</script>
@endpush
