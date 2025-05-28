<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Controllers\ProdutoController;
use App\Services\EstoqueService;
use App\Models\Produto;
use App\Models\ProdutoVariacao;
use App\Http\Requests\StoreProdutoRequest;
use App\Http\Requests\UpdateProdutoRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Mockery;

class ProdutoControllerTest extends TestCase
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

    public function test_controller_estrutura_basica()
    {
        $this->assertTrue(method_exists($this->controller, 'index'));
        $this->assertTrue(method_exists($this->controller, 'create'));
        $this->assertTrue(method_exists($this->controller, 'store'));
        $this->assertTrue(method_exists($this->controller, 'show'));
        $this->assertTrue(method_exists($this->controller, 'edit'));
        $this->assertTrue(method_exists($this->controller, 'update'));
        $this->assertTrue(method_exists($this->controller, 'destroy'));
        $this->assertTrue(method_exists($this->controller, 'adicionarVariacao'));

        $this->assertInstanceOf(EstoqueService::class, $this->estoqueService);

        $this->assertInstanceOf(ProdutoController::class, $this->controller);
    }

    public function test_create_retorna_view_de_criacao()
    {
        $response = $this->controller->create();

        $this->assertInstanceOf(View::class, $response);
        $this->assertEquals('produtos.create', $response->getName());
    }

    public function test_controller_tem_dependencias_corretas()
    {
        $reflection = new \ReflectionClass($this->controller);
        $constructor = $reflection->getConstructor();
        $parameters = $constructor->getParameters();

        $this->assertCount(1, $parameters);
        $this->assertEquals('estoqueService', $parameters[0]->getName());
        $this->assertEquals(EstoqueService::class, $parameters[0]->getType()->getName());
    }

    public function test_controller_metodos_sao_publicos()
    {
        $reflection = new \ReflectionClass($this->controller);

        $this->assertTrue($reflection->getMethod('index')->isPublic());
        $this->assertTrue($reflection->getMethod('create')->isPublic());
        $this->assertTrue($reflection->getMethod('store')->isPublic());
        $this->assertTrue($reflection->getMethod('show')->isPublic());
        $this->assertTrue($reflection->getMethod('edit')->isPublic());
        $this->assertTrue($reflection->getMethod('update')->isPublic());
        $this->assertTrue($reflection->getMethod('destroy')->isPublic());
        $this->assertTrue($reflection->getMethod('adicionarVariacao')->isPublic());
    }

    public function test_controller_service_injetado_corretamente()
    {
        $reflection = new \ReflectionClass($this->controller);
        $property = $reflection->getProperty('estoqueService');
        $property->setAccessible(true);

        $service = $property->getValue($this->controller);

        $this->assertSame($this->estoqueService, $service);
    }

    public function test_metodos_dependem_de_models_testados_indiretamente()
    {
        $reflection = new \ReflectionClass($this->controller);

        $this->assertCount(0, $reflection->getMethod('index')->getParameters());
        $this->assertCount(0, $reflection->getMethod('create')->getParameters());
        $this->assertCount(1, $reflection->getMethod('store')->getParameters());
        $this->assertCount(1, $reflection->getMethod('show')->getParameters());
        $this->assertCount(1, $reflection->getMethod('edit')->getParameters());
        $this->assertCount(2, $reflection->getMethod('update')->getParameters());
        $this->assertCount(1, $reflection->getMethod('destroy')->getParameters());
        $this->assertCount(2, $reflection->getMethod('adicionarVariacao')->getParameters());
    }
}
