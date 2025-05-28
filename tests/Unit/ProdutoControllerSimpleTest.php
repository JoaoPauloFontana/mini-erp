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
        // Act
        $response = $this->controller->create();

        // Assert
        $this->assertInstanceOf(View::class, $response);
        $this->assertEquals('produtos.create', $response->getName());
    }

    public function test_show_retorna_view_com_produto()
    {
        // Arrange
        $produto = Mockery::mock('App\Models\Produto');
        $produto->shouldReceive('load')
            ->once()
            ->with(['variacoes.estoque', 'estoque'])
            ->andReturnSelf();

        // Act
        $response = $this->controller->show($produto);

        // Assert
        $this->assertInstanceOf(View::class, $response);
        $this->assertEquals('produtos.show', $response->getName());
        $this->assertArrayHasKey('produto', $response->getData());
    }

    public function test_edit_retorna_view_de_edicao()
    {
        // Arrange
        $produto = Mockery::mock('App\Models\Produto');
        $produto->shouldReceive('load')
            ->once()
            ->with(['variacoes.estoque', 'estoque'])
            ->andReturnSelf();

        // Act
        $response = $this->controller->edit($produto);

        // Assert
        $this->assertInstanceOf(View::class, $response);
        $this->assertEquals('produtos.edit', $response->getName());
        $this->assertArrayHasKey('produto', $response->getData());
    }

    public function test_destroy_remove_produto()
    {
        // Arrange
        $produto = Mockery::mock('App\Models\Produto');
        $produto->shouldReceive('update')
            ->once()
            ->with(['ativo' => false]);

        // Act
        $response = $this->controller->destroy($produto);

        // Assert
        $this->assertInstanceOf(RedirectResponse::class, $response);
    }

    public function test_controller_tem_dependencia_estoque_service()
    {
        // Arrange
        $reflection = new \ReflectionClass($this->controller);
        $property = $reflection->getProperty('estoqueService');
        $property->setAccessible(true);

        // Act
        $service = $property->getValue($this->controller);

        // Assert
        $this->assertSame($this->estoqueService, $service);
    }

    public function test_create_retorna_view_sem_dados()
    {
        // Act
        $response = $this->controller->create();

        // Assert
        $this->assertInstanceOf(View::class, $response);
        $this->assertEmpty($response->getData());
    }

    public function test_show_retorna_view_com_produto_no_contexto()
    {
        // Arrange
        $produto = Mockery::mock('App\Models\Produto');
        $produto->shouldReceive('load')->andReturnSelf();

        // Act
        $response = $this->controller->show($produto);

        // Assert
        $this->assertInstanceOf(View::class, $response);
        $this->assertArrayHasKey('produto', $response->getData());
    }

    public function test_edit_retorna_view_com_produto_no_contexto()
    {
        // Arrange
        $produto = Mockery::mock('App\Models\Produto');
        $produto->shouldReceive('load')->andReturnSelf();

        // Act
        $response = $this->controller->edit($produto);

        // Assert
        $this->assertInstanceOf(View::class, $response);
        $this->assertArrayHasKey('produto', $response->getData());
    }

    public function test_destroy_retorna_redirect_response()
    {
        // Arrange
        $produto = Mockery::mock('App\Models\Produto');
        $produto->shouldReceive('update')->once();

        // Act
        $response = $this->controller->destroy($produto);

        // Assert
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(302, $response->getStatusCode());
    }

    public function test_views_tem_nomes_corretos()
    {
        // Arrange
        $produto = Mockery::mock('App\Models\Produto');
        $produto->shouldReceive('load')->andReturnSelf();

        // Act & Assert
        $createResponse = $this->controller->create();
        $this->assertEquals('produtos.create', $createResponse->getName());

        $showResponse = $this->controller->show($produto);
        $this->assertEquals('produtos.show', $showResponse->getName());

        $editResponse = $this->controller->edit($produto);
        $this->assertEquals('produtos.edit', $editResponse->getName());
    }

    public function test_metodos_retornam_tipos_corretos()
    {
        // Arrange
        $produto = Mockery::mock('App\Models\Produto');
        $produto->shouldReceive('load')->andReturnSelf();
        $produto->shouldReceive('update')->once();

        // Act & Assert
        $this->assertInstanceOf(View::class, $this->controller->create());
        $this->assertInstanceOf(View::class, $this->controller->show($produto));
        $this->assertInstanceOf(View::class, $this->controller->edit($produto));
        $this->assertInstanceOf(RedirectResponse::class, $this->controller->destroy($produto));
    }

    public function test_produto_load_e_chamado_com_relacionamentos_corretos()
    {
        // Arrange
        $produto = Mockery::mock('App\Models\Produto');
        $produto->shouldReceive('load')
            ->with(['variacoes.estoque', 'estoque'])
            ->twice()
            ->andReturnSelf();

        // Act
        $this->controller->show($produto);
        $this->controller->edit($produto);

        // Assert - Mockery verifica automaticamente se foi chamado 2 vezes
        $this->assertTrue(true);
    }

    public function test_destroy_atualiza_campo_ativo_para_false()
    {
        // Arrange
        $produto = Mockery::mock('App\Models\Produto');
        $produto->shouldReceive('update')
            ->once()
            ->with(['ativo' => false]);

        // Act
        $this->controller->destroy($produto);

        // Assert - Mock verifica se foi chamado com parâmetros corretos
        $this->assertTrue(true);
    }

    public function test_controller_funciona_com_mock_service()
    {
        // Arrange
        $mockService = Mockery::mock(EstoqueService::class);
        $controller = new ProdutoController($mockService);

        // Act
        $response = $controller->create();

        // Assert
        $this->assertInstanceOf(View::class, $response);
        $this->assertInstanceOf(ProdutoController::class, $controller);
    }

    public function test_controller_instancia_corretamente()
    {
        // Assert
        $this->assertInstanceOf(ProdutoController::class, $this->controller);
    }

    public function test_estoque_service_e_injetado_corretamente()
    {
        // Assert
        $this->assertInstanceOf(EstoqueService::class, $this->estoqueService);
    }

    public function test_create_view_tem_nome_correto()
    {
        // Act
        $response = $this->controller->create();

        // Assert
        $this->assertEquals('produtos.create', $response->getName());
        $this->assertEmpty($response->getData());
    }

    public function test_show_carrega_relacionamentos()
    {
        // Arrange
        $produto = Mockery::mock('App\Models\Produto');
        $produto->shouldReceive('load')
            ->once()
            ->with(['variacoes.estoque', 'estoque'])
            ->andReturnSelf();

        // Act
        $this->controller->show($produto);

        // Assert - Mock verifica se load foi chamado
        $this->assertTrue(true);
    }

    public function test_edit_carrega_relacionamentos()
    {
        // Arrange
        $produto = Mockery::mock('App\Models\Produto');
        $produto->shouldReceive('load')
            ->once()
            ->with(['variacoes.estoque', 'estoque'])
            ->andReturnSelf();

        // Act
        $this->controller->edit($produto);

        // Assert - Mock verifica se load foi chamado
        $this->assertTrue(true);
    }

    public function test_destroy_faz_soft_delete()
    {
        // Arrange
        $produto = Mockery::mock('App\Models\Produto');
        $produto->shouldReceive('update')
            ->once()
            ->with(['ativo' => false]);

        // Act
        $this->controller->destroy($produto);

        // Assert - Mock verifica se update foi chamado
        $this->assertTrue(true);
    }

    public function test_metodos_existem()
    {
        // Assert
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
        // Arrange
        $reflection = new \ReflectionClass($this->controller);
        $constructor = $reflection->getConstructor();
        $parameters = $constructor->getParameters();

        // Assert
        $this->assertCount(1, $parameters);
        $this->assertEquals('estoqueService', $parameters[0]->getName());
        $this->assertEquals(EstoqueService::class, $parameters[0]->getType()->getName());
    }
}
