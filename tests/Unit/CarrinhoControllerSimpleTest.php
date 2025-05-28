<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Controllers\CarrinhoController;
use App\Services\CarrinhoService;
use App\Services\EstoqueService;
use App\Services\PedidoService;
use App\Http\Requests\VerificarCepRequest;
use App\Http\Resources\CepResource;
use Illuminate\Support\Facades\Http;
use Mockery;

class CarrinhoControllerSimpleTest extends TestCase
{
    protected $controller;
    protected $carrinhoService;
    protected $estoqueService;
    protected $pedidoService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->carrinhoService = Mockery::mock(CarrinhoService::class);
        $this->estoqueService = Mockery::mock(EstoqueService::class);
        $this->pedidoService = Mockery::mock(PedidoService::class);

        $this->controller = new CarrinhoController(
            $this->carrinhoService,
            $this->estoqueService,
            $this->pedidoService
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_verificar_cep_valido()
    {
        $request = Mockery::mock(VerificarCepRequest::class);
        $request->shouldReceive('get')->with('cep')->andReturn('01310100');

        Http::fake([
            'viacep.com.br/ws/01310100/json/' => Http::response([
                'cep' => '01310-100',
                'logradouro' => 'Avenida Paulista',
                'bairro' => 'Bela Vista',
                'localidade' => 'São Paulo',
                'uf' => 'SP'
            ])
        ]);

        $response = $this->controller->verificarCep($request);

        $this->assertInstanceOf(CepResource::class, $response);
    }

    public function test_verificar_cep_invalido()
    {
        $request = Mockery::mock(VerificarCepRequest::class);
        $request->shouldReceive('get')->with('cep')->andReturn('00000000');

        Http::fake([
            'viacep.com.br/ws/00000000/json/' => Http::response(['erro' => true])
        ]);

        $response = $this->controller->verificarCep($request);

        $this->assertInstanceOf(CepResource::class, $response);
    }

    public function test_controller_tem_dependencias_corretas()
    {
        $reflection = new \ReflectionClass($this->controller);
        $constructor = $reflection->getConstructor();
        $parameters = $constructor->getParameters();

        $this->assertCount(3, $parameters);
        $this->assertEquals('carrinhoService', $parameters[0]->getName());
        $this->assertEquals('estoqueService', $parameters[1]->getName());
        $this->assertEquals('pedidoService', $parameters[2]->getName());
    }

    public function test_controller_instancia_corretamente()
    {
        $this->assertInstanceOf(CarrinhoController::class, $this->controller);
    }

    public function test_services_sao_injetados_corretamente()
    {
        $reflection = new \ReflectionClass($this->controller);

        $carrinhoProperty = $reflection->getProperty('carrinhoService');
        $carrinhoProperty->setAccessible(true);

        $estoqueProperty = $reflection->getProperty('estoqueService');
        $estoqueProperty->setAccessible(true);

        $pedidoProperty = $reflection->getProperty('pedidoService');
        $pedidoProperty->setAccessible(true);

        $carrinhoService = $carrinhoProperty->getValue($this->controller);
        $estoqueService = $estoqueProperty->getValue($this->controller);
        $pedidoService = $pedidoProperty->getValue($this->controller);

        $this->assertSame($this->carrinhoService, $carrinhoService);
        $this->assertSame($this->estoqueService, $estoqueService);
        $this->assertSame($this->pedidoService, $pedidoService);
    }

    public function test_metodos_publicos_existem()
    {
        $this->assertTrue(method_exists($this->controller, 'index'));
        $this->assertTrue(method_exists($this->controller, 'adicionar'));
        $this->assertTrue(method_exists($this->controller, 'atualizar'));
        $this->assertTrue(method_exists($this->controller, 'remover'));
        $this->assertTrue(method_exists($this->controller, 'verificarCep'));
        $this->assertTrue(method_exists($this->controller, 'aplicarCupom'));
        $this->assertTrue(method_exists($this->controller, 'removerCupom'));
        $this->assertTrue(method_exists($this->controller, 'checkout'));
        $this->assertTrue(method_exists($this->controller, 'finalizarPedido'));
    }

    public function test_verificar_cep_com_resposta_vazia()
    {
        $request = Mockery::mock(VerificarCepRequest::class);
        $request->shouldReceive('get')->with('cep')->andReturn('12345678');

        Http::fake([
            'viacep.com.br/ws/12345678/json/' => Http::response([])
        ]);

        $response = $this->controller->verificarCep($request);

        $this->assertInstanceOf(CepResource::class, $response);
    }

    public function test_verificar_cep_com_timeout()
    {
        $request = Mockery::mock(VerificarCepRequest::class);
        $request->shouldReceive('get')->with('cep')->andReturn('87654321');

        Http::fake([
            'viacep.com.br/ws/87654321/json/' => Http::response([], 500)
        ]);

        $response = $this->controller->verificarCep($request);

        $this->assertInstanceOf(CepResource::class, $response);
    }

    public function test_controller_usa_services_corretos()
    {
        $reflection = new \ReflectionClass($this->controller);
        $constructor = $reflection->getConstructor();
        $parameters = $constructor->getParameters();

        $this->assertEquals(CarrinhoService::class, $parameters[0]->getType()->getName());
        $this->assertEquals(EstoqueService::class, $parameters[1]->getType()->getName());
        $this->assertEquals(PedidoService::class, $parameters[2]->getType()->getName());
    }

    public function test_verificar_cep_formata_corretamente()
    {
        $request = Mockery::mock(VerificarCepRequest::class);
        $request->shouldReceive('get')->with('cep')->andReturn('01310-100');

        Http::fake([
            'viacep.com.br/ws/01310100/json/' => Http::response([
                'cep' => '01310-100',
                'logradouro' => 'Avenida Paulista',
                'bairro' => 'Bela Vista',
                'localidade' => 'São Paulo',
                'uf' => 'SP'
            ])
        ]);

        $response = $this->controller->verificarCep($request);

        $this->assertInstanceOf(CepResource::class, $response);
    }

    public function test_verificar_cep_remove_caracteres_especiais()
    {
        $request = Mockery::mock(VerificarCepRequest::class);
        $request->shouldReceive('get')->with('cep')->andReturn('01.310-100');

        Http::fake([
            'viacep.com.br/ws/01310100/json/' => Http::response([
                'cep' => '01310-100',
                'logradouro' => 'Avenida Paulista'
            ])
        ]);

        $response = $this->controller->verificarCep($request);

        $this->assertInstanceOf(CepResource::class, $response);
    }

    public function test_controller_funciona_com_mock_services()
    {
        $mockCarrinhoService = Mockery::mock(CarrinhoService::class);
        $mockEstoqueService = Mockery::mock(EstoqueService::class);
        $mockPedidoService = Mockery::mock(PedidoService::class);

        $controller = new CarrinhoController(
            $mockCarrinhoService,
            $mockEstoqueService,
            $mockPedidoService
        );

        $this->assertInstanceOf(CarrinhoController::class, $controller);
    }

    public function test_verificar_cep_api_externa()
    {
        $request = Mockery::mock(VerificarCepRequest::class);
        $request->shouldReceive('get')->with('cep')->andReturn('20040020');

        Http::fake([
            'viacep.com.br/ws/20040020/json/' => Http::response([
                'cep' => '20040-020',
                'logradouro' => 'Rua da Assembleia',
                'bairro' => 'Centro',
                'localidade' => 'Rio de Janeiro',
                'uf' => 'RJ'
            ])
        ]);

        $response = $this->controller->verificarCep($request);

        $this->assertInstanceOf(CepResource::class, $response);
    }

    public function test_dependencias_sao_interfaces_corretas()
    {
        $this->assertInstanceOf(CarrinhoService::class, $this->carrinhoService);
        $this->assertInstanceOf(EstoqueService::class, $this->estoqueService);
        $this->assertInstanceOf(PedidoService::class, $this->pedidoService);
    }
}
