<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Controllers\WebhookController;
use App\Services\PedidoService;
use App\Http\Requests\WebhookStatusRequest;
use App\Http\Requests\TesteWebhookRequest;
use App\Http\Resources\WebhookResource;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;
use Mockery;

class WebhookControllerSimpleTest extends TestCase
{
    protected $controller;
    protected $pedidoService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->pedidoService = Mockery::mock(PedidoService::class);
        $this->controller = new WebhookController($this->pedidoService);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_controller_instancia_corretamente()
    {
        // Assert
        $this->assertInstanceOf(WebhookController::class, $this->controller);
    }

    public function test_pedido_service_e_injetado_corretamente()
    {
        // Arrange
        $reflection = new \ReflectionClass($this->controller);
        $property = $reflection->getProperty('pedidoService');
        $property->setAccessible(true);

        // Act
        $service = $property->getValue($this->controller);

        // Assert
        $this->assertSame($this->pedidoService, $service);
    }

    public function test_controller_tem_dependencia_pedido_service()
    {
        // Arrange
        $reflection = new \ReflectionClass($this->controller);
        $constructor = $reflection->getConstructor();
        $parameters = $constructor->getParameters();

        // Assert
        $this->assertCount(1, $parameters);
        $this->assertEquals('pedidoService', $parameters[0]->getName());
        $this->assertEquals(PedidoService::class, $parameters[0]->getType()->getName());
    }

    public function test_metodos_publicos_existem()
    {
        // Assert
        $this->assertTrue(method_exists($this->controller, 'receberStatus'));
        $this->assertTrue(method_exists($this->controller, 'testarWebhookForm'));
        $this->assertTrue(method_exists($this->controller, 'testarWebhookPost'));
    }

    public function test_controller_usa_service_correto()
    {
        // Arrange
        $reflection = new \ReflectionClass($this->controller);
        $constructor = $reflection->getConstructor();
        $parameters = $constructor->getParameters();

        // Assert
        $this->assertEquals(PedidoService::class, $parameters[0]->getType()->getName());
    }

    public function test_controller_funciona_com_mock_service()
    {
        // Arrange
        $mockService = Mockery::mock(PedidoService::class);
        $controller = new WebhookController($mockService);

        // Assert
        $this->assertInstanceOf(WebhookController::class, $controller);
    }

    public function test_service_e_instancia_correta()
    {
        // Assert
        $this->assertInstanceOf(PedidoService::class, $this->pedidoService);
    }

    public function test_controller_estrutura_correta()
    {
        // Arrange
        $reflection = new \ReflectionClass($this->controller);

        // Assert
        $this->assertTrue($reflection->hasMethod('receberStatus'));
        $this->assertTrue($reflection->hasMethod('testarWebhookForm'));
        $this->assertTrue($reflection->hasMethod('testarWebhookPost'));
        $this->assertTrue($reflection->hasProperty('pedidoService'));
    }

    public function test_metodos_sao_publicos()
    {
        // Arrange
        $reflection = new \ReflectionClass($this->controller);

        // Assert
        $this->assertTrue($reflection->getMethod('receberStatus')->isPublic());
        $this->assertTrue($reflection->getMethod('testarWebhookForm')->isPublic());
        $this->assertTrue($reflection->getMethod('testarWebhookPost')->isPublic());
    }

    public function test_propriedade_service_e_protegida()
    {
        // Arrange
        $reflection = new \ReflectionClass($this->controller);
        $property = $reflection->getProperty('pedidoService');

        // Assert
        $this->assertTrue($property->isProtected());
    }

    public function test_controller_namespace_correto()
    {
        // Arrange
        $reflection = new \ReflectionClass($this->controller);

        // Assert
        $this->assertEquals('App\Http\Controllers', $reflection->getNamespaceName());
    }

    public function test_controller_herda_de_controller_base()
    {
        // Arrange
        $reflection = new \ReflectionClass($this->controller);
        $parentClass = $reflection->getParentClass();

        // Assert
        $this->assertNotFalse($parentClass);
        $this->assertEquals('App\Http\Controllers\Controller', $parentClass->getName());
    }

    public function test_metodos_existem_e_sao_chamaveiss()
    {
        // Arrange
        $reflection = new \ReflectionClass($this->controller);
        $receberStatusMethod = $reflection->getMethod('receberStatus');
        $testarWebhookFormMethod = $reflection->getMethod('testarWebhookForm');
        $testarWebhookPostMethod = $reflection->getMethod('testarWebhookPost');

        // Assert
        $this->assertTrue($receberStatusMethod->isPublic());
        $this->assertTrue($testarWebhookFormMethod->isPublic());
        $this->assertTrue($testarWebhookPostMethod->isPublic());
        $this->assertNotNull($receberStatusMethod);
        $this->assertNotNull($testarWebhookFormMethod);
        $this->assertNotNull($testarWebhookPostMethod);
    }

    public function test_constructor_tem_parametros_corretos()
    {
        // Arrange
        $reflection = new \ReflectionClass($this->controller);
        $constructor = $reflection->getConstructor();
        $parameters = $constructor->getParameters();

        // Assert
        $this->assertCount(1, $parameters);
        $this->assertTrue($parameters[0]->hasType());
        $this->assertFalse($parameters[0]->isOptional());
    }

    public function test_dependencia_e_obrigatoria()
    {
        // Arrange
        $reflection = new \ReflectionClass($this->controller);
        $constructor = $reflection->getConstructor();
        $parameters = $constructor->getParameters();

        // Assert
        $this->assertFalse($parameters[0]->isOptional());
        $this->assertFalse($parameters[0]->allowsNull());
    }

    public function test_controller_implementa_interface_correta()
    {
        // Arrange
        $reflection = new \ReflectionClass($this->controller);

        // Assert
        $this->assertTrue($reflection->isInstantiable());
        $this->assertFalse($reflection->isAbstract());
        $this->assertFalse($reflection->isInterface());
    }

    public function test_metodos_tem_parametros_corretos()
    {
        // Arrange
        $reflection = new \ReflectionClass($this->controller);
        $receberStatusMethod = $reflection->getMethod('receberStatus');
        $testarWebhookFormMethod = $reflection->getMethod('testarWebhookForm');
        $testarWebhookPostMethod = $reflection->getMethod('testarWebhookPost');

        // Assert
        $this->assertCount(1, $receberStatusMethod->getParameters());
        $this->assertCount(0, $testarWebhookFormMethod->getParameters()); // GET não tem parâmetros
        $this->assertCount(1, $testarWebhookPostMethod->getParameters()); // POST tem request
    }

    public function test_service_property_existe()
    {
        // Arrange
        $reflection = new \ReflectionClass($this->controller);

        // Assert
        $this->assertTrue($reflection->hasProperty('pedidoService'));
    }

    public function test_controller_pode_ser_instanciado()
    {
        // Arrange
        $mockService = Mockery::mock(PedidoService::class);

        // Act
        $controller = new WebhookController($mockService);

        // Assert
        $this->assertInstanceOf(WebhookController::class, $controller);
        $this->assertNotNull($controller);
    }

    public function test_reflection_funciona_corretamente()
    {
        // Arrange
        $reflection = new \ReflectionClass($this->controller);

        // Assert
        $this->assertEquals('WebhookController', $reflection->getShortName());
        $this->assertEquals('App\Http\Controllers\WebhookController', $reflection->getName());
    }
}
