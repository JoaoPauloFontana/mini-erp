<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use App\Models\Estoque;
use App\Models\Cupom;
use App\Models\Pedido;
use App\Models\PedidoItem;
use App\Http\Requests\StoreCarrinhoRequest;
use App\Http\Requests\AtualizarCarrinhoRequest;
use App\Http\Requests\RemoverCarrinhoRequest;
use App\Http\Requests\FinalizarPedidoRequest;
use App\Http\Requests\AplicarCupomRequest;
use App\Http\Requests\VerificarCepRequest;
use App\Http\Resources\CarrinhoResource;
use App\Http\Resources\CepResource;
use App\Services\CarrinhoService;
use App\Services\EstoqueService;
use App\Services\PedidoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CarrinhoController extends Controller
{
    protected CarrinhoService $carrinhoService;
    protected EstoqueService $estoqueService;
    protected PedidoService $pedidoService;

    public function __construct(
        CarrinhoService $carrinhoService,
        EstoqueService $estoqueService,
        PedidoService $pedidoService
    ) {
        $this->carrinhoService = $carrinhoService;
        $this->estoqueService = $estoqueService;
        $this->pedidoService = $pedidoService;
    }

    public function index()
    {
        $carrinho = session('carrinho', []);
        $totais = $this->carrinhoService->calcularTotais($carrinho);
        $cupom = session('cupom_aplicado');

        return view('carrinho.index', compact('carrinho', 'totais', 'cupom'));
    }

    public function adicionar(StoreCarrinhoRequest $request)
    {
        $produto = Produto::find($request->produto_id);

        if (!$this->estoqueService->verificarDisponibilidade(
            $request->produto_id,
            $request->variacao_id,
            $request->quantidade
        )) {
            return CarrinhoResource::error('Estoque insuficiente');
        }

        $carrinho = session('carrinho', []);
        $chave = $this->carrinhoService->gerarChaveItem($request->produto_id, $request->variacao_id);

        if (isset($carrinho[$chave])) {
            $carrinho[$chave]['quantidade'] += $request->quantidade;
            $this->carrinhoService->atualizarSubtotal($carrinho[$chave]);
        } else {
            $carrinho[$chave] = $this->carrinhoService->criarItemCarrinho(
                $produto,
                $request->variacao_id,
                $request->quantidade
            );
        }

        session(['carrinho' => $carrinho]);

        $quantidadeTotal = $this->carrinhoService->calcularQuantidadeTotal($carrinho);

        return CarrinhoResource::success([
            'quantidade_carrinho' => $quantidadeTotal
        ], 'Produto adicionado ao carrinho!');
    }

    public function atualizar(AtualizarCarrinhoRequest $request)
    {
        $carrinho = session('carrinho', []);

        if (!isset($carrinho[$request->chave])) {
            return CarrinhoResource::error('Item não encontrado no carrinho');
        }

        if ($request->quantidade == 0) {
            unset($carrinho[$request->chave]);
        } else {
            $carrinho[$request->chave]['quantidade'] = $request->quantidade;
            $carrinho[$request->chave]['subtotal'] =
                $carrinho[$request->chave]['preco_unitario'] * $request->quantidade;
        }

        session(['carrinho' => $carrinho]);

        $totais = $this->carrinhoService->calcularTotais($carrinho);
        $quantidadeTotal = $this->carrinhoService->calcularQuantidadeTotal($carrinho);

        return CarrinhoResource::success([
            'totais' => $totais,
            'quantidade_carrinho' => $quantidadeTotal
        ], 'Carrinho atualizado!');
    }

    public function remover(RemoverCarrinhoRequest $request)
    {
        $carrinho = session('carrinho', []);

        if (isset($carrinho[$request->chave])) {
            unset($carrinho[$request->chave]);
            session(['carrinho' => $carrinho]);
        }

        $totais = $this->carrinhoService->calcularTotais($carrinho);
        $quantidadeTotal = $this->carrinhoService->calcularQuantidadeTotal($carrinho);

        return CarrinhoResource::success([
            'totais' => $totais,
            'quantidade_carrinho' => $quantidadeTotal
        ], 'Item removido do carrinho!');
    }

    public function verificarCep(VerificarCepRequest $request)
    {

        try {
            $response = Http::get("https://viacep.com.br/ws/{$request->cep}/json/");

            if ($response->successful()) {
                $data = $response->json();

                if (isset($data['erro'])) {
                    return CepResource::error('CEP não encontrado');
                }

                return CepResource::success(['cep_data' => $data], 'CEP encontrado');
            }
        } catch (\Exception $e) {
            return CepResource::error('Erro ao consultar CEP');
        }
    }

    public function aplicarCupom(AplicarCupomRequest $request)
    {

        $cupom = Cupom::where('codigo', strtoupper($request->codigo))
            ->ativo()
            ->valido()
            ->first();

        if (!$cupom) {
            return CarrinhoResource::error('Cupom não encontrado ou inválido');
        }

        $carrinho = session('carrinho', []);
        $subtotal = array_sum(array_column($carrinho, 'subtotal'));

        $validacao = $cupom->validar($subtotal);

        if (!$validacao['valido']) {
            return CarrinhoResource::error($validacao['erro']);
        }

        $desconto = $cupom->calcularDesconto($subtotal);

        session([
            'cupom_aplicado' => $cupom->toArray(),
            'desconto' => $desconto
        ]);

        $totais = $this->carrinhoService->calcularTotais($carrinho);

        return CarrinhoResource::success([
            'totais' => $totais
        ], 'Cupom aplicado com sucesso!');
    }

    public function removerCupom()
    {
        session()->forget(['cupom_aplicado', 'desconto']);

        $carrinho = session('carrinho', []);
        $totais = $this->carrinhoService->calcularTotais($carrinho);

        return CarrinhoResource::success([
            'totais' => $totais
        ], 'Cupom removido');
    }

    public function checkout()
    {
        $carrinho = session('carrinho', []);

        if (empty($carrinho)) {
            return redirect()->route('produtos.index')
                ->with('error', 'Carrinho vazio');
        }

        $totais = $this->carrinhoService->calcularTotais($carrinho);
        $cupom = session('cupom_aplicado');

        return view('carrinho.checkout', compact('carrinho', 'totais', 'cupom'));
    }

    public function finalizarPedido(FinalizarPedidoRequest $request)
    {
        $carrinho = session('carrinho', []);

        if ($this->carrinhoService->carrinhoVazio($carrinho)) {
            return redirect()->route('produtos.index')
                ->with('error', 'Carrinho vazio');
        }

        try {
            $pedido = $this->pedidoService->criarPedido($request, $carrinho);

            session(['pedido_criado' => $pedido->id]);

            return redirect()->route('produtos.index')
                ->with('success', 'Pedido realizado com sucesso! Você receberá um email de confirmação.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Erro ao processar pedido: ' . $e->getMessage());
        }
    }


}
