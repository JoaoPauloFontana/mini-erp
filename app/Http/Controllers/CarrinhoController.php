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
use Exception;
use App\Constants\SystemConstants;

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
        $carrinho = session(SystemConstants::SESSION_CARRINHO, []);
        $totais = $this->carrinhoService->calcularTotais($carrinho);
        $cupom = session(SystemConstants::SESSION_CUPOM_APLICADO);

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
            return CarrinhoResource::error(SystemConstants::MSG_ESTOQUE_INSUFICIENTE);
        }

        $carrinho = session(SystemConstants::SESSION_CARRINHO, []);
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

        session([SystemConstants::SESSION_CARRINHO => $carrinho]);

        $quantidadeTotal = $this->carrinhoService->calcularQuantidadeTotal($carrinho);

        return CarrinhoResource::success([
            'quantidade_carrinho' => $quantidadeTotal
        ], SystemConstants::MSG_PRODUTO_ADICIONADO_CARRINHO);
    }

    public function atualizar(AtualizarCarrinhoRequest $request)
    {
        $carrinho = session(SystemConstants::SESSION_CARRINHO, []);

        if (!isset($carrinho[$request->chave])) {
            return CarrinhoResource::error(SystemConstants::MSG_ITEM_NAO_ENCONTRADO);
        }

        if ($request->quantidade == SystemConstants::ZERO_QUANTITY) {
            unset($carrinho[$request->chave]);
        } else {
            $carrinho[$request->chave]['quantidade'] = $request->quantidade;
            $carrinho[$request->chave]['subtotal'] =
                $carrinho[$request->chave]['preco_unitario'] * $request->quantidade;
        }

        session([SystemConstants::SESSION_CARRINHO => $carrinho]);

        $totais = $this->carrinhoService->calcularTotais($carrinho);
        $quantidadeTotal = $this->carrinhoService->calcularQuantidadeTotal($carrinho);

        return CarrinhoResource::success([
            'totais' => $totais,
            'quantidade_carrinho' => $quantidadeTotal
        ], SystemConstants::MSG_CARRINHO_ATUALIZADO);
    }

    public function remover(RemoverCarrinhoRequest $request)
    {
        $carrinho = session(SystemConstants::SESSION_CARRINHO, []);

        if (isset($carrinho[$request->chave])) {
            unset($carrinho[$request->chave]);
            session([SystemConstants::SESSION_CARRINHO => $carrinho]);
        }

        $totais = $this->carrinhoService->calcularTotais($carrinho);
        $quantidadeTotal = $this->carrinhoService->calcularQuantidadeTotal($carrinho);

        return CarrinhoResource::success([
            'totais' => $totais,
            'quantidade_carrinho' => $quantidadeTotal
        ], SystemConstants::MSG_ITEM_REMOVIDO_CARRINHO);
    }

    public function verificarCep(VerificarCepRequest $request)
    {

        try {
            $response = Http::get(SystemConstants::VIACEP_URL . "/{$request->cep}/json/");

            if ($response->successful()) {
                $data = $response->json();

                if (isset($data['erro'])) {
                    return CepResource::error(SystemConstants::MSG_CEP_NAO_ENCONTRADO);
                }

                return CepResource::success(['cep_data' => $data], SystemConstants::MSG_CEP_ENCONTRADO);
            }
        } catch (Exception $e) {
            return CepResource::error(SystemConstants::MSG_ERRO_CONSULTAR_CEP);
        }
    }

    public function aplicarCupom(AplicarCupomRequest $request)
    {

        $cupom = Cupom::where('codigo', strtoupper($request->codigo))
            ->ativo()
            ->valido()
            ->first();

        if (!$cupom) {
            return CarrinhoResource::error(SystemConstants::MSG_CUPOM_INVALIDO);
        }

        $carrinho = session(SystemConstants::SESSION_CARRINHO, []);
        $subtotal = array_sum(array_column($carrinho, 'subtotal'));

        $validacao = $cupom->validar($subtotal);

        if (!$validacao['valido']) {
            return CarrinhoResource::error($validacao['erro']);
        }

        $desconto = $cupom->calcularDesconto($subtotal);

        session([
            SystemConstants::SESSION_CUPOM_APLICADO => $cupom->toArray(),
            SystemConstants::SESSION_DESCONTO => $desconto
        ]);

        $totais = $this->carrinhoService->calcularTotais($carrinho);

        return CarrinhoResource::success([
            'totais' => $totais
        ], SystemConstants::MSG_CUPOM_APLICADO);
    }

    public function removerCupom()
    {
        session()->forget([SystemConstants::SESSION_CUPOM_APLICADO, SystemConstants::SESSION_DESCONTO]);

        $carrinho = session(SystemConstants::SESSION_CARRINHO, []);
        $totais = $this->carrinhoService->calcularTotais($carrinho);

        return CarrinhoResource::success([
            'totais' => $totais
        ], SystemConstants::MSG_CUPOM_REMOVIDO);
    }

    public function checkout()
    {
        $carrinho = session(SystemConstants::SESSION_CARRINHO, []);

        if (empty($carrinho)) {
            return redirect()->route('produtos.index')
                ->with('error', SystemConstants::MSG_CARRINHO_VAZIO);
        }

        $totais = $this->carrinhoService->calcularTotais($carrinho);
        $cupom = session(SystemConstants::SESSION_CUPOM_APLICADO);

        return view('carrinho.checkout', compact('carrinho', 'totais', 'cupom'));
    }

    public function finalizarPedido(FinalizarPedidoRequest $request)
    {
        $carrinho = session(SystemConstants::SESSION_CARRINHO, []);

        if ($this->carrinhoService->carrinhoVazio($carrinho)) {
            return redirect()->route('produtos.index')
                ->with('error', SystemConstants::MSG_CARRINHO_VAZIO);
        }

        try {
            $pedido = $this->pedidoService->criarPedido($request, $carrinho);

            session([SystemConstants::SESSION_PEDIDO_CRIADO => $pedido->id]);

            return redirect()->route('produtos.index')
                ->with('success', SystemConstants::MSG_PEDIDO_REALIZADO);

        } catch (Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', SystemConstants::MSG_ERRO_PROCESSAR_PEDIDO . $e->getMessage());
        }
    }
}
