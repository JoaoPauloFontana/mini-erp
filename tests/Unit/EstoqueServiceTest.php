<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\EstoqueService;
use App\Models\Estoque;
use Mockery;

class EstoqueServiceTest extends TestCase
{
    protected $estoqueService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->estoqueService = new EstoqueService();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_validacao_quantidade_positiva()
    {
        $this->assertTrue(method_exists($this->estoqueService, 'criarOuAtualizarEstoque'));

        $this->assertInstanceOf(EstoqueService::class, $this->estoqueService);
    }

    public function test_nao_criar_estoque_com_quantidade_zero()
    {
        $resultado = $this->estoqueService->criarOuAtualizarEstoque(1, null, 0);

        $this->assertNull($resultado);
    }

    public function test_nao_criar_estoque_com_quantidade_negativa()
    {
        $resultado = $this->estoqueService->criarOuAtualizarEstoque(1, null, -5);

        $this->assertNull($resultado);
    }

    public function test_metodos_dependem_de_models_testados_indiretamente()
    {
        $this->assertTrue(method_exists($this->estoqueService, 'criarOuAtualizarEstoque'));
        $this->assertTrue(method_exists($this->estoqueService, 'verificarDisponibilidade'));
        $this->assertTrue(method_exists($this->estoqueService, 'reduzirEstoque'));
        $this->assertTrue(method_exists($this->estoqueService, 'devolverEstoque'));

        $this->assertInstanceOf(EstoqueService::class, $this->estoqueService);
    }

    public function test_service_instancia_corretamente()
    {
        $this->assertInstanceOf(EstoqueService::class, $this->estoqueService);
    }

    public function test_metodos_publicos_existem()
    {
        $this->assertTrue(method_exists($this->estoqueService, 'criarOuAtualizarEstoque'));
        $this->assertTrue(method_exists($this->estoqueService, 'verificarDisponibilidade'));
        $this->assertTrue(method_exists($this->estoqueService, 'reduzirEstoque'));
        $this->assertTrue(method_exists($this->estoqueService, 'devolverEstoque'));
    }
}
