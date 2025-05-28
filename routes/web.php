<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\CarrinhoController;
use App\Http\Controllers\WebhookController;

Route::get('/', function () {
    return redirect()->route('produtos.index');
});

Route::resource('produtos', ProdutoController::class);
Route::post('produtos/{produto}/variacao', [ProdutoController::class, 'adicionarVariacao'])->name('produtos.adicionar-variacao');

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

Route::post('/webhook/status', [WebhookController::class, 'receberStatus'])->name('webhook.status');
Route::get('/webhook/teste', [WebhookController::class, 'testarWebhookForm'])->name('webhook.teste');
Route::post('/webhook/teste', [WebhookController::class, 'testarWebhookPost'])->name('webhook.teste.post');
