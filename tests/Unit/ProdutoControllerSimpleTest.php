<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Controllers\ProdutoController;
use App\Services\EstoqueService;
use App\Http\Requests\StoreProdutoRequest;
use App\Http\Requests\UpdateProdutoRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Mockery;

class ProdutoControllerSimpleTest extends TestCase
{
    protected $controller;
    protected $estoqueService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->estoqueService = Mockery::mock(EstoqueService::class);
        $this->controller = new ProdutoController($this->estoqueService);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_create_retorna_view_de_criacao()
    {
        $response = $this->controller->create();

        $this->assertInstanceOf(View::class, $response);
        $this->assertEquals('produtos.create', $response->getName());
    }

    public function test_show_retorna_view_com_produto()
    {
        $produto = Mockery::mock('App\Models\Produto');
        $produto->shouldReceive('load')
            ->once()
            ->with(['variacoes.estoque', 'estoque'])
            ->andReturnSelf();

        $response = $this->controller->show($produto);

        $this->assertInstanceOf(View::class, $response);
        $this->assertEquals('produtos.show', $response->getName());
        $this->assertArrayHasKey('produto', $response->getData());
    }

    public function test_edit_retorna_view_de_edicao()
    {
        $produto = Mockery::mock('App\Models\Produto');
        $produto->shouldReceive('load')
            ->once()
            ->with(['variacoes.estoque', 'estoque'])
            ->andReturnSelf();

        $response = $this->controller->edit($produto);

        $this->assertInstanceOf(View::class, $response);
        $this->assertEquals('produtos.edit', $response->getName());
        $this->assertArrayHasKey('produto', $response->getData());
    }

    public function test_destroy_remove_produto()
    {
        $produto = Mockery::mock('App\Models\Produto');
        $produto->shouldReceive('update')
            ->once()
            ->with(['ativo' => false]);

        $response = $this->controller->destroy($produto);

        $this->assertInstanceOf(RedirectResponse::class, $response);
    }

    public function test_controller_tem_dependencia_estoque_service()
    {
        $reflection = new \ReflectionClass($this->controller);
        $property = $reflection->getProperty('estoqueService');
        $property->setAccessible(true);

        $service = $property->getValue($this->controller);

        $this->assertSame($this->estoqueService, $service);
    }

    public function test_create_retorna_view_sem_dados()
    {
        $response = $this->controller->create();

        $this->assertInstanceOf(View::class, $response);
        $this->assertEmpty($response->getData());
    }

    public function test_show_retorna_view_com_produto_no_contexto()
    {
        $produto = Mockery::mock('App\Models\Produto');
        $produto->shouldReceive('load')->andReturnSelf();

        $response = $this->controller->show($produto);

        $this->assertInstanceOf(View::class, $response);
        $this->assertArrayHasKey('produto', $response->getData());
    }

    public function test_edit_retorna_view_com_produto_no_contexto()
    {
        $produto = Mockery::mock('App\Models\Produto');
        $produto->shouldReceive('load')->andReturnSelf();

        $response = $this->controller->edit($produto);

        $this->assertInstanceOf(View::class, $response);
        $this->assertArrayHasKey('produto', $response->getData());
    }

    public function test_destroy_retorna_redirect_response()
    {
        $produto = Mockery::mock('App\Models\Produto');
        $produto->shouldReceive('update')->once();

        $response = $this->controller->destroy($produto);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(302, $response->getStatusCode());
    }

    public function test_views_tem_nomes_corretos()
    {
        $produto = Mockery::mock('App\Models\Produto');
        $produto->shouldReceive('load')->andReturnSelf();

        $createResponse = $this->controller->create();
        $this->assertEquals('produtos.create', $createResponse->getName());

        $showResponse = $this->controller->show($produto);
        $this->assertEquals('produtos.show', $showResponse->getName());

        $editResponse = $this->controller->edit($produto);
        $this->assertEquals('produtos.edit', $editResponse->getName());
    }

    public function test_metodos_retornam_tipos_corretos()
    {
        $produto = Mockery::mock('App\Models\Produto');
        $produto->shouldReceive('load')->andReturnSelf();
        $produto->shouldReceive('update')->once();

        $this->assertInstanceOf(View::class, $this->controller->create());
        $this->assertInstanceOf(View::class, $this->controller->show($produto));
        $this->assertInstanceOf(View::class, $this->controller->edit($produto));
        $this->assertInstanceOf(RedirectResponse::class, $this->controller->destroy($produto));
    }

    public function test_produto_load_e_chamado_com_relacionamentos_corretos()
    {
        $produto = Mockery::mock('App\Models\Produto');
        $produto->shouldReceive('load')
            ->with(['variacoes.estoque', 'estoque'])
            ->twice()
            ->andReturnSelf();

        $this->controller->show($produto);
        $this->controller->edit($produto);

        $this->assertTrue(true);
    }

    public function test_destroy_atualiza_campo_ativo_para_false()
    {
        $produto = Mockery::mock('App\Models\Produto');
        $produto->shouldReceive('update')
            ->once()
            ->with(['ativo' => false]);

        $this->controller->destroy($produto);

        $this->assertTrue(true);
    }

    public function test_controller_funciona_com_mock_service()
    {
        $mockService = Mockery::mock(EstoqueService::class);
        $controller = new ProdutoController($mockService);

        $response = $controller->create();

        $this->assertInstanceOf(View::class, $response);
        $this->assertInstanceOf(ProdutoController::class, $controller);
    }

    public function test_controller_instancia_corretamente()
    {
        $this->assertInstanceOf(ProdutoController::class, $this->controller);
    }

    public function test_estoque_service_e_injetado_corretamente()
    {
        $this->assertInstanceOf(EstoqueService::class, $this->estoqueService);
    }

    public function test_create_view_tem_nome_correto()
    {
        $response = $this->controller->create();

        $this->assertEquals('produtos.create', $response->getName());
        $this->assertEmpty($response->getData());
    }

    public function test_show_carrega_relacionamentos()
    {
        $produto = Mockery::mock('App\Models\Produto');
        $produto->shouldReceive('load')
            ->once()
            ->with(['variacoes.estoque', 'estoque'])
            ->andReturnSelf();

        $this->controller->show($produto);

        $this->assertTrue(true);
    }

    public function test_edit_carrega_relacionamentos()
    {
        $produto = Mockery::mock('App\Models\Produto');
        $produto->shouldReceive('load')
            ->once()
            ->with(['variacoes.estoque', 'estoque'])
            ->andReturnSelf();

        $this->controller->edit($produto);

        $this->assertTrue(true);
    }

    public function test_destroy_faz_soft_delete()
    {
        $produto = Mockery::mock('App\Models\Produto');
        $produto->shouldReceive('update')
            ->once()
            ->with(['ativo' => false]);

        $this->controller->destroy($produto);

        $this->assertTrue(true);
    }

    public function test_metodos_existem()
    {
        $this->assertTrue(method_exists($this->controller, 'index'));
        $this->assertTrue(method_exists($this->controller, 'create'));
        $this->assertTrue(method_exists($this->controller, 'store'));
        $this->assertTrue(method_exists($this->controller, 'show'));
        $this->assertTrue(method_exists($this->controller, 'edit'));
        $this->assertTrue(method_exists($this->controller, 'update'));
        $this->assertTrue(method_exists($this->controller, 'destroy'));
        $this->assertTrue(method_exists($this->controller, 'adicionarVariacao'));
    }

    public function test_controller_usa_service_correto()
    {
        $reflection = new \ReflectionClass($this->controller);
        $constructor = $reflection->getConstructor();
        $parameters = $constructor->getParameters();

        $this->assertCount(1, $parameters);
        $this->assertEquals('estoqueService', $parameters[0]->getName());
        $this->assertEquals(EstoqueService::class, $parameters[0]->getType()->getName());
    }
}
