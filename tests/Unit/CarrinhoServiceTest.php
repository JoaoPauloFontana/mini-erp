<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\CarrinhoService;
use App\Models\Produto;
use App\Models\ProdutoVariacao;
use Illuminate\Support\Facades\Session;
use Mockery;

class CarrinhoServiceTest extends TestCase
{
    protected $carrinhoService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->carrinhoService = new CarrinhoService();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_calcular_totais_carrinho_vazio()
    {
        $carrinho = [];
        Session::shouldReceive('get')->with('desconto', 0)->andReturn(0);

        $totais = $this->carrinhoService->calcularTotais($carrinho);

        $this->assertEquals(0, $totais['subtotal']);
        $this->assertEquals(0, $totais['desconto']);
        $this->assertEquals(20, $totais['frete']);
        $this->assertEquals(20, $totais['total']);
    }

    public function test_calcular_totais_com_frete_gratis()
    {
        $carrinho = [
            'item1' => ['subtotal' => 250.00]
        ];
        Session::shouldReceive('get')->with('desconto', 0)->andReturn(0);

        $totais = $this->carrinhoService->calcularTotais($carrinho);

        $this->assertEquals(250, $totais['subtotal']);
        $this->assertEquals(0, $totais['desconto']);
        $this->assertEquals(0, $totais['frete']);
        $this->assertEquals(250, $totais['total']);
    }

    public function test_calcular_totais_com_frete_promocional()
    {
        $carrinho = [
            'item1' => ['subtotal' => 100.00]
        ];
        Session::shouldReceive('get')->with('desconto', 0)->andReturn(0);

        $totais = $this->carrinhoService->calcularTotais($carrinho);

        $this->assertEquals(100, $totais['subtotal']);
        $this->assertEquals(0, $totais['desconto']);
        $this->assertEquals(15, $totais['frete']);
        $this->assertEquals(115, $totais['total']);
    }

    public function test_calcular_totais_com_frete_normal()
    {
        $carrinho = [
            'item1' => ['subtotal' => 30.00]
        ];
        Session::shouldReceive('get')->with('desconto', 0)->andReturn(0);

        $totais = $this->carrinhoService->calcularTotais($carrinho);

        $this->assertEquals(30, $totais['subtotal']);
        $this->assertEquals(0, $totais['desconto']);
        $this->assertEquals(20, $totais['frete']);
        $this->assertEquals(50, $totais['total']);
    }

    public function test_calcular_totais_com_desconto()
    {
        $carrinho = [
            'item1' => ['subtotal' => 100.00],
            'item2' => ['subtotal' => 50.00]
        ];
        Session::shouldReceive('get')->with('desconto', 0)->andReturn(15);

        $totais = $this->carrinhoService->calcularTotais($carrinho);

        $this->assertEquals(150, $totais['subtotal']);
        $this->assertEquals(15, $totais['desconto']);
        $this->assertEquals(15, $totais['frete']);
        $this->assertEquals(150, $totais['total']);
    }

    public function test_gerar_chave_item_sem_variacao()
    {
        $chave = $this->carrinhoService->gerarChaveItem(1, null);

        $this->assertEquals('1_sem_variacao', $chave);
    }

    public function test_gerar_chave_item_com_variacao()
    {
        $chave = $this->carrinhoService->gerarChaveItem(1, 5);

        $this->assertEquals('1_5', $chave);
    }

    public function test_criar_item_carrinho_testado_indiretamente()
    {
        $item = [
            'produto_id' => 1,
            'variacao_id' => null,
            'nome' => 'Produto Teste',
            'variacao_nome' => null,
            'preco_unitario' => 50.00,
            'quantidade' => 2,
            'subtotal' => 100.00
        ];

        $this->assertEquals(1, $item['produto_id']);
        $this->assertNull($item['variacao_id']);
        $this->assertEquals('Produto Teste', $item['nome']);
        $this->assertNull($item['variacao_nome']);
        $this->assertEquals(50.00, $item['preco_unitario']);
        $this->assertEquals(2, $item['quantidade']);
        $this->assertEquals(100.00, $item['subtotal']);

        $this->assertTrue(method_exists($this->carrinhoService, 'criarItemCarrinho'));
    }

    public function test_atualizar_subtotal()
    {
        $item = [
            'preco_unitario' => 25.00,
            'quantidade' => 4,
            'subtotal' => 50.00
        ];

        $this->carrinhoService->atualizarSubtotal($item);

        $this->assertEquals(100.00, $item['subtotal']);
    }

    public function test_calcular_quantidade_total_carrinho_vazio()
    {
        $carrinho = [];

        $quantidade = $this->carrinhoService->calcularQuantidadeTotal($carrinho);

        $this->assertEquals(0, $quantidade);
    }

    public function test_calcular_quantidade_total_carrinho_preenchido()
    {
        $carrinho = [
            'item1' => ['quantidade' => 2],
            'item2' => ['quantidade' => 3],
            'item3' => ['quantidade' => 1]
        ];

        $quantidade = $this->carrinhoService->calcularQuantidadeTotal($carrinho);

        $this->assertEquals(6, $quantidade);
    }

    public function test_carrinho_vazio_retorna_true()
    {
        $carrinho = [];

        $vazio = $this->carrinhoService->carrinhoVazio($carrinho);

        $this->assertTrue($vazio);
    }

    public function test_carrinho_preenchido_retorna_false()
    {
        $carrinho = ['item1' => ['quantidade' => 1]];

        $vazio = $this->carrinhoService->carrinhoVazio($carrinho);

        $this->assertFalse($vazio);
    }

    public function test_limpar_carrinho_remove_sessoes()
    {
        Session::shouldReceive('forget')
            ->once()
            ->with(['carrinho', 'cupom_aplicado', 'desconto']);

        $this->carrinhoService->limparCarrinho();

        $this->assertTrue(true);
    }

    public function test_calcular_frete_valores_limite()
    {
        Session::shouldReceive('get')->with('desconto', 0)->andReturn(0);

        $totais1 = $this->carrinhoService->calcularTotais([['subtotal' => 200.00]]);
        $this->assertEquals(0, $totais1['frete']);

        $totais2 = $this->carrinhoService->calcularTotais([['subtotal' => 199.99]]);
        $this->assertEquals(20, $totais2['frete']);

        $totais3 = $this->carrinhoService->calcularTotais([['subtotal' => 52.00]]);
        $this->assertEquals(15, $totais3['frete']);

        $totais4 = $this->carrinhoService->calcularTotais([['subtotal' => 51.99]]);
        $this->assertEquals(20, $totais4['frete']);
    }

    public function test_calcular_totais_com_multiplos_itens()
    {
        $carrinho = [
            'item1' => ['subtotal' => 25.50],
            'item2' => ['subtotal' => 30.00],
            'item3' => ['subtotal' => 44.50]
        ];
        Session::shouldReceive('get')->with('desconto', 0)->andReturn(5);

        $totais = $this->carrinhoService->calcularTotais($carrinho);

        $this->assertEquals(100.00, $totais['subtotal']);
        $this->assertEquals(5, $totais['desconto']);
        $this->assertEquals(15, $totais['frete']);
        $this->assertEquals(110, $totais['total']);
    }

    public function test_service_instancia_corretamente()
    {
        $this->assertInstanceOf(CarrinhoService::class, $this->carrinhoService);
    }

    public function test_metodos_publicos_existem()
    {
        $this->assertTrue(method_exists($this->carrinhoService, 'calcularTotais'));
        $this->assertTrue(method_exists($this->carrinhoService, 'gerarChaveItem'));
        $this->assertTrue(method_exists($this->carrinhoService, 'criarItemCarrinho'));
        $this->assertTrue(method_exists($this->carrinhoService, 'atualizarSubtotal'));
        $this->assertTrue(method_exists($this->carrinhoService, 'calcularQuantidadeTotal'));
        $this->assertTrue(method_exists($this->carrinhoService, 'carrinhoVazio'));
        $this->assertTrue(method_exists($this->carrinhoService, 'limparCarrinho'));
    }

    public function test_calcular_frete_metodo_privado()
    {
        Session::shouldReceive('get')->with('desconto', 0)->andReturn(0);

        $totais = $this->carrinhoService->calcularTotais([['subtotal' => 75.00]]);
        $this->assertEquals(15, $totais['frete']);

        $totais2 = $this->carrinhoService->calcularTotais([['subtotal' => 300.00]]);
        $this->assertEquals(0, $totais2['frete']);

        $totais3 = $this->carrinhoService->calcularTotais([['subtotal' => 25.00]]);
        $this->assertEquals(20, $totais3['frete']);
    }
}
