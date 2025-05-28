@extends('layouts.app')

@section('title', 'Teste Webhook - Mini ERP')

@section('content')
<div class="row">
    <div class="col-md-8">
        <h1><i class="bi bi-webhook"></i> Teste do Webhook</h1>
        
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Testar Webhook de Status</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('webhook.teste') }}">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="pedido_id" class="form-label">ID do Pedido</label>
                            @if($pedidos->isNotEmpty())
                                <select class="form-select @error('pedido_id') is-invalid @enderror" 
                                        id="pedido_id" name="pedido_id" required>
                                    <option value="">Selecione um pedido</option>
                                    @foreach($pedidos as $pedido)
                                        <option value="{{ $pedido->id }}" {{ old('pedido_id') == $pedido->id ? 'selected' : '' }}>
                                            #{{ $pedido->id }} - {{ $pedido->numero_pedido }} ({{ ucfirst($pedido->status) }})
                                        </option>
                                    @endforeach
                                </select>
                            @else
                                <input type="number" class="form-control @error('pedido_id') is-invalid @enderror" 
                                       id="pedido_id" name="pedido_id" value="{{ old('pedido_id') }}" 
                                       placeholder="Digite o ID do pedido" required>
                                <div class="form-text">Nenhum pedido encontrado. Digite o ID manualmente.</div>
                            @endif
                            @error('pedido_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Novo Status</label>
                            <select class="form-select @error('status') is-invalid @enderror" 
                                    id="status" name="status" required>
                                <option value="">Selecione o status</option>
                                <option value="pendente" {{ old('status') == 'pendente' ? 'selected' : '' }}>Pendente</option>
                                <option value="confirmado" {{ old('status') == 'confirmado' ? 'selected' : '' }}>Confirmado</option>
                                <option value="enviado" {{ old('status') == 'enviado' ? 'selected' : '' }}>Enviado</option>
                                <option value="entregue" {{ old('status') == 'entregue' ? 'selected' : '' }}>Entregue</option>
                                <option value="cancelado" {{ old('status') == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-send"></i> Executar Webhook
                    </button>
                </form>
            </div>
        </div>

        @if(isset($response))
            <div class="card mt-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Resposta do Webhook</h5>
                    <span class="badge {{ $status_code == 200 ? 'bg-success' : 'bg-danger' }}">
                        HTTP {{ $status_code }}
                    </span>
                </div>
                <div class="card-body">
                    <pre class="bg-light p-3 rounded"><code>{{ $response }}</code></pre>
                </div>
            </div>
        @endif
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-info-circle"></i> Documentação</h5>
            </div>
            <div class="card-body">
                <h6>Endpoint do Webhook</h6>
                <code>POST {{ route('webhook.status') }}</code>
                
                <h6 class="mt-3">Parâmetros</h6>
                <ul class="list-unstyled">
                    <li><strong>pedido_id:</strong> ID do pedido (integer)</li>
                    <li><strong>status:</strong> Novo status (string)</li>
                </ul>
                
                <h6 class="mt-3">Status Aceitos</h6>
                <ul class="list-unstyled">
                    <li>• pendente</li>
                    <li>• confirmado</li>
                    <li>• enviado</li>
                    <li>• entregue</li>
                    <li>• cancelado</li>
                </ul>
                
                <h6 class="mt-3">Comportamento</h6>
                <ul class="list-unstyled small">
                    <li>• <strong>cancelado:</strong> Remove o pedido e devolve o estoque</li>
                    <li>• <strong>outros:</strong> Atualiza apenas o status</li>
                </ul>
            </div>
        </div>

        @if($pedidos->isNotEmpty())
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="mb-0">Pedidos Recentes</h6>
                </div>
                <div class="card-body">
                    @foreach($pedidos as $pedido)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <strong>#{{ $pedido->id }}</strong><br>
                                <small class="text-muted">{{ $pedido->numero_pedido }}</small>
                            </div>
                            <span class="badge bg-{{ 
                                $pedido->status == 'pendente' ? 'warning' : 
                                ($pedido->status == 'confirmado' ? 'info' : 
                                ($pedido->status == 'enviado' ? 'primary' : 
                                ($pedido->status == 'entregue' ? 'success' : 'danger'))) 
                            }}">
                                {{ ucfirst($pedido->status) }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
