<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\PedidoService;
use App\Services\EstoqueService;
use App\Services\CarrinhoService;
use Mockery;

class PedidoServiceSimpleTest extends TestCase
{
    protected $pedidoService;
    protected $estoqueService;
    protected $carrinhoService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->estoqueService = Mockery::mock(EstoqueService::class);
        $this->carrinhoService = Mockery::mock(CarrinhoService::class);
        $this->pedidoService = new PedidoService($this->estoqueService, $this->carrinhoService);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_service_instancia_corretamente()
    {
        // Assert
        $this->assertInstanceOf(PedidoService::class, $this->pedidoService);
    }

    public function test_services_sao_injetados_corretamente()
    {
        // Arrange
        $reflection = new \ReflectionClass($this->pedidoService);
        
        $estoqueProperty = $reflection->getProperty('estoqueService');
        $estoqueProperty->setAccessible(true);
        
        $carrinhoProperty = $reflection->getProperty('carrinhoService');
        $carrinhoProperty->setAccessible(true);

        // Act
        $estoqueService = $estoqueProperty->getValue($this->pedidoService);
        $carrinhoService = $carrinhoProperty->getValue($this->pedidoService);

        // Assert
        $this->assertSame($this->estoqueService, $estoqueService);
        $this->assertSame($this->carrinhoService, $carrinhoService);
    }

    public function test_service_tem_dependencias_corretas()
    {
        // Arrange
        $reflection = new \ReflectionClass($this->pedidoService);
        $constructor = $reflection->getConstructor();
        $parameters = $constructor->getParameters();

        // Assert
        $this->assertCount(2, $parameters);
        $this->assertEquals('estoqueService', $parameters[0]->getName());
        $this->assertEquals('carrinhoService', $parameters[1]->getName());
        $this->assertEquals(EstoqueService::class, $parameters[0]->getType()->getName());
        $this->assertEquals(CarrinhoService::class, $parameters[1]->getType()->getName());
    }

    public function test_metodos_publicos_existem()
    {
        // Assert
        $this->assertTrue(method_exists($this->pedidoService, 'criarPedido'));
        $this->assertTrue(method_exists($this->pedidoService, 'atualizarStatus'));
        $this->assertTrue(method_exists($this->pedidoService, 'cancelarPedido'));
        $this->assertTrue(method_exists($this->pedidoService, 'criarRegistroPedido'));
        $this->assertTrue(method_exists($this->pedidoService, 'processarItensCarrinho'));
        $this->assertTrue(method_exists($this->pedidoService, 'incrementarUsoCupom'));
        $this->assertTrue(method_exists($this->pedidoService, 'logConfirmacao'));
    }

    public function test_service_funciona_com_mock_dependencies()
    {
        // Arrange
        $mockEstoqueService = Mockery::mock(EstoqueService::class);
        $mockCarrinhoService = Mockery::mock(CarrinhoService::class);
        
        $service = new PedidoService($mockEstoqueService, $mockCarrinhoService);

        // Assert
        $this->assertInstanceOf(PedidoService::class, $service);
    }

    public function test_dependencies_sao_interfaces_corretas()
    {
        // Assert
        $this->assertInstanceOf(EstoqueService::class, $this->estoqueService);
        $this->assertInstanceOf(CarrinhoService::class, $this->carrinhoService);
    }

    public function test_service_estrutura_correta()
    {
        // Arrange
        $reflection = new \ReflectionClass($this->pedidoService);

        // Assert
        $this->assertTrue($reflection->hasMethod('criarPedido'));
        $this->assertTrue($reflection->hasMethod('atualizarStatus'));
        $this->assertTrue($reflection->hasMethod('cancelarPedido'));
        $this->assertTrue($reflection->hasProperty('estoqueService'));
        $this->assertTrue($reflection->hasProperty('carrinhoService'));
    }

    public function test_metodos_sao_publicos()
    {
        // Arrange
        $reflection = new \ReflectionClass($this->pedidoService);

        // Assert
        $this->assertTrue($reflection->getMethod('criarPedido')->isPublic());
        $this->assertTrue($reflection->getMethod('atualizarStatus')->isPublic());
        $this->assertTrue($reflection->getMethod('cancelarPedido')->isPublic());
    }

    public function test_propriedades_services_sao_protegidas()
    {
        // Arrange
        $reflection = new \ReflectionClass($this->pedidoService);

        // Assert
        $this->assertTrue($reflection->getProperty('estoqueService')->isProtected());
        $this->assertTrue($reflection->getProperty('carrinhoService')->isProtected());
    }

    public function test_service_namespace_correto()
    {
        // Arrange
        $reflection = new \ReflectionClass($this->pedidoService);

        // Assert
        $this->assertEquals('App\Services', $reflection->getNamespaceName());
    }

    public function test_constructor_tem_parametros_corretos()
    {
        // Arrange
        $reflection = new \ReflectionClass($this->pedidoService);
        $constructor = $reflection->getConstructor();
        $parameters = $constructor->getParameters();

        // Assert
        $this->assertCount(2, $parameters);
        $this->assertTrue($parameters[0]->hasType());
        $this->assertTrue($parameters[1]->hasType());
        $this->assertFalse($parameters[0]->isOptional());
        $this->assertFalse($parameters[1]->isOptional());
    }

    public function test_dependencias_sao_obrigatorias()
    {
        // Arrange
        $reflection = new \ReflectionClass($this->pedidoService);
        $constructor = $reflection->getConstructor();
        $parameters = $constructor->getParameters();

        // Assert
        $this->assertFalse($parameters[0]->isOptional());
        $this->assertFalse($parameters[0]->allowsNull());
        $this->assertFalse($parameters[1]->isOptional());
        $this->assertFalse($parameters[1]->allowsNull());
    }

    public function test_service_implementa_interface_correta()
    {
        // Arrange
        $reflection = new \ReflectionClass($this->pedidoService);

        // Assert
        $this->assertTrue($reflection->isInstantiable());
        $this->assertFalse($reflection->isAbstract());
        $this->assertFalse($reflection->isInterface());
    }

    public function test_metodos_tem_parametros_esperados()
    {
        // Arrange
        $reflection = new \ReflectionClass($this->pedidoService);

        // Assert
        $this->assertCount(2, $reflection->getMethod('criarPedido')->getParameters());
        $this->assertCount(2, $reflection->getMethod('atualizarStatus')->getParameters());
        $this->assertCount(1, $reflection->getMethod('cancelarPedido')->getParameters());
    }

    public function test_service_pode_ser_instanciado()
    {
        // Arrange
        $mockEstoqueService = Mockery::mock(EstoqueService::class);
        $mockCarrinhoService = Mockery::mock(CarrinhoService::class);

        // Act
        $service = new PedidoService($mockEstoqueService, $mockCarrinhoService);

        // Assert
        $this->assertInstanceOf(PedidoService::class, $service);
        $this->assertNotNull($service);
    }

    public function test_reflection_funciona_corretamente()
    {
        // Arrange
        $reflection = new \ReflectionClass($this->pedidoService);

        // Assert
        $this->assertEquals('PedidoService', $reflection->getShortName());
        $this->assertEquals('App\Services\PedidoService', $reflection->getName());
    }

    public function test_metodos_protegidos_existem()
    {
        // Arrange
        $reflection = new \ReflectionClass($this->pedidoService);

        // Assert
        $this->assertTrue($reflection->hasMethod('criarRegistroPedido'));
        $this->assertTrue($reflection->hasMethod('processarItensCarrinho'));
        $this->assertTrue($reflection->hasMethod('incrementarUsoCupom'));
        $this->assertTrue($reflection->hasMethod('logConfirmacao'));
    }

    public function test_service_usa_services_corretos()
    {
        // Arrange
        $reflection = new \ReflectionClass($this->pedidoService);
        $constructor = $reflection->getConstructor();
        $parameters = $constructor->getParameters();

        // Assert
        $this->assertEquals(EstoqueService::class, $parameters[0]->getType()->getName());
        $this->assertEquals(CarrinhoService::class, $parameters[1]->getType()->getName());
    }

    public function test_service_tem_todos_metodos_necessarios()
    {
        // Arrange
        $expectedMethods = [
            'criarPedido',
            'atualizarStatus', 
            'cancelarPedido',
            'criarRegistroPedido',
            'processarItensCarrinho',
            'incrementarUsoCupom',
            'logConfirmacao'
        ];

        // Act & Assert
        foreach ($expectedMethods as $method) {
            $this->assertTrue(method_exists($this->pedidoService, $method), "Método {$method} não existe");
        }
    }

    public function test_service_properties_existem()
    {
        // Arrange
        $reflection = new \ReflectionClass($this->pedidoService);

        // Assert
        $this->assertTrue($reflection->hasProperty('estoqueService'));
        $this->assertTrue($reflection->hasProperty('carrinhoService'));
    }
}
