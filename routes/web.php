<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\CarrinhoController;
use App\Http\Controllers\WebhookController;

// Página inicial - redireciona para produtos
Route::get('/', function () {
    return redirect()->route('produtos.index');
});

// Rotas de produtos
Route::resource('produtos', ProdutoController::class);
Route::post('produtos/{produto}/variacao', [ProdutoController::class, 'adicionarVariacao'])->name('produtos.adicionar-variacao');

// Rotas do carrinho
Route::prefix('carrinho')->name('carrinho.')->group(function () {
    Route::get('/', [CarrinhoController::class, 'index'])->name('index');
    Route::post('/adicionar', [CarrinhoController::class, 'adicionar'])->name('adicionar');
    Route::post('/atualizar', [CarrinhoController::class, 'atualizar'])->name('atualizar');
    Route::post('/remover', [CarrinhoController::class, 'remover'])->name('remover');
    Route::get('/checkout', [CarrinhoController::class, 'checkout'])->name('checkout');
    Route::post('/checkout', [CarrinhoController::class, 'finalizarPedido'])->name('finalizar');
    Route::post('/cupom/aplicar', [CarrinhoController::class, 'aplicarCupom'])->name('cupom.aplicar');
    Route::post('/cupom/remover', [CarrinhoController::class, 'removerCupom'])->name('cupom.remover');
    Route::post('/cep', [CarrinhoController::class, 'verificarCep'])->name('cep');
});

// Webhook
Route::post('/webhook/status', [WebhookController::class, 'receberStatus'])->name('webhook.status');
Route::get('/webhook/teste', [WebhookController::class, 'testarWebhookForm'])->name('webhook.teste');
Route::post('/webhook/teste', [WebhookController::class, 'testarWebhookPost'])->name('webhook.teste.post');
