<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use App\Models\ProdutoVariacao;
use App\Models\Estoque;
use App\Http\Requests\StoreProdutoRequest;
use App\Http\Requests\UpdateProdutoRequest;
use App\Services\EstoqueService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProdutoController extends Controller
{
    protected EstoqueService $estoqueService;

    public function __construct(EstoqueService $estoqueService)
    {
        $this->estoqueService = $estoqueService;
    }
    
    public function index()
    {
        $produtos = Produto::ativo()
            ->with(['estoque', 'variacoes.estoque'])
            ->orderBy('nome')
            ->get();

        return view('produtos.index', compact('produtos'));
    }

    public function create()
    {
        return view('produtos.create');
    }

    public function store(StoreProdutoRequest $request)
    {

        DB::transaction(function () use ($request) {
            $produto = Produto::create([
                'nome' => $request->nome,
                'preco' => $request->preco,
                'descricao' => $request->descricao,
            ]);

            if ($request->estoque_inicial > 0) {
                $this->estoqueService->criarOuAtualizarEstoque(
                    $produto->id,
                    null,
                    $request->estoque_inicial
                );
            }

            if ($request->variacoes) {
                foreach ($request->variacoes as $variacaoData) {
                    if (!empty($variacaoData['nome'])) {
                        $variacao = ProdutoVariacao::create([
                            'produto_id' => $produto->id,
                            'nome' => $variacaoData['nome'],
                            'valor_adicional' => $variacaoData['valor_adicional'] ?? 0,
                        ]);

                        if (isset($variacaoData['estoque']) && $variacaoData['estoque'] > 0) {
                            $this->estoqueService->criarOuAtualizarEstoque(
                                $produto->id,
                                $variacao->id,
                                $variacaoData['estoque']
                            );
                        }
                    }
                }
            }
        });

        return redirect()->route('produtos.index')
            ->with('success', 'Produto criado com sucesso!');
    }

    public function show(Produto $produto)
    {
        $produto->load(['variacoes.estoque', 'estoque']);
        return view('produtos.show', compact('produto'));
    }

    public function adicionarVariacao(UpdateProdutoRequest $request, Produto $produto)
    {
        DB::transaction(function () use ($request, $produto) {
            $variacao = ProdutoVariacao::create([
                'produto_id' => $produto->id,
                'nome' => $request->variacao_nome,
                'valor_adicional' => $request->variacao_valor_adicional ?? 0,
            ]);

            if ($request->variacao_estoque > 0) {
                $this->estoqueService->criarOuAtualizarEstoque(
                    $produto->id,
                    $variacao->id,
                    $request->variacao_estoque
                );
            }
        });

        return redirect()->route('produtos.show', $produto)
            ->with('success', 'Variação adicionada com sucesso!');
    }

    public function edit(Produto $produto)
    {
        $produto->load(['variacoes.estoque', 'estoque']);
        return view('produtos.edit', compact('produto'));
    }

    public function update(UpdateProdutoRequest $request, Produto $produto)
    {

        DB::transaction(function () use ($request, $produto) {
            $produto->update([
                'nome' => $request->nome,
                'preco' => $request->preco,
                'descricao' => $request->descricao,
            ]);

            if ($request->has('estoque_principal')) {
                $estoque = $produto->estoque()->whereNull('variacao_id')->first();
                if ($estoque) {
                    $estoque->update(['quantidade' => $request->estoque_principal]);
                } else {
                    Estoque::create([
                        'produto_id' => $produto->id,
                        'variacao_id' => null,
                        'quantidade' => $request->estoque_principal,
                    ]);
                }
            }

            if ($request->has('variacoes')) {
                foreach ($request->variacoes as $variacao_id => $variacaoData) {
                    $variacao = ProdutoVariacao::find($variacao_id);
                    if ($variacao && $variacao->produto_id == $produto->id) {
                        $variacao->update([
                            'nome' => $variacaoData['nome'],
                            'valor_adicional' => $variacaoData['valor_adicional'] ?? 0,
                        ]);

                        if (isset($variacaoData['estoque'])) {
                            $estoque = $produto->estoque()->where('variacao_id', $variacao_id)->first();
                            if ($estoque) {
                                $estoque->update(['quantidade' => $variacaoData['estoque']]);
                            } else {
                                Estoque::create([
                                    'produto_id' => $produto->id,
                                    'variacao_id' => $variacao_id,
                                    'quantidade' => $variacaoData['estoque'],
                                ]);
                            }
                        }
                    }
                }
            }
        });

        return redirect()->route('produtos.show', $produto)
            ->with('success', 'Produto atualizado com sucesso!');
    }

    public function destroy(Produto $produto)
    {
        $produto->update(['ativo' => false]);

        return redirect()->route('produtos.index')
            ->with('success', 'Produto removido com sucesso!');
    }
}
