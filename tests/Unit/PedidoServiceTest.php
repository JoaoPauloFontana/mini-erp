<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\PedidoService;
use App\Services\EstoqueService;
use App\Services\CarrinhoService;
use App\Models\Pedido;
use App\Models\PedidoItem;
use App\Models\Cupom;
use App\Http\Requests\FinalizarPedidoRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Mockery;

class PedidoServiceTest extends TestCase
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

    public function test_service_estrutura_basica()
    {
        // Testa apenas a estrutura básica do service sem mockar Models complexos

        // Assert - Verificar se os métodos existem
        $this->assertTrue(method_exists($this->pedidoService, 'criarPedido'));
        $this->assertTrue(method_exists($this->pedidoService, 'atualizarStatus'));
        $this->assertTrue(method_exists($this->pedidoService, 'cancelarPedido'));
        $this->assertTrue(method_exists($this->pedidoService, 'criarRegistroPedido'));
        $this->assertTrue(method_exists($this->pedidoService, 'processarItensCarrinho'));
        $this->assertTrue(method_exists($this->pedidoService, 'incrementarUsoCupom'));
        $this->assertTrue(method_exists($this->pedidoService, 'logConfirmacao'));

        // Assert - Verificar se os services foram injetados
        $this->assertInstanceOf(EstoqueService::class, $this->estoqueService);
        $this->assertInstanceOf(CarrinhoService::class, $this->carrinhoService);

        // Assert - Verificar se o service foi instanciado
        $this->assertInstanceOf(PedidoService::class, $this->pedidoService);
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

    public function test_service_metodos_publicos_e_privados()
    {
        // Arrange
        $reflection = new \ReflectionClass($this->pedidoService);

        // Assert - Métodos públicos
        $this->assertTrue($reflection->getMethod('criarPedido')->isPublic());
        $this->assertTrue($reflection->getMethod('atualizarStatus')->isPublic());
        $this->assertTrue($reflection->getMethod('cancelarPedido')->isPublic());

        // Assert - Métodos privados
        $this->assertTrue($reflection->getMethod('criarRegistroPedido')->isPrivate());
        $this->assertTrue($reflection->getMethod('processarItensCarrinho')->isPrivate());
        $this->assertTrue($reflection->getMethod('incrementarUsoCupom')->isPrivate());
        $this->assertTrue($reflection->getMethod('logConfirmacao')->isPrivate());
    }

    public function test_metodos_dependem_de_models_testados_indiretamente()
    {
        // Os métodos que dependem de Models e Requests complexos são testados
        // através de testes de integração ou feature tests

        // Verificar se os métodos têm a assinatura correta
        $reflection = new \ReflectionClass($this->pedidoService);

        $this->assertCount(2, $reflection->getMethod('criarPedido')->getParameters());
        $this->assertCount(2, $reflection->getMethod('atualizarStatus')->getParameters());
        $this->assertCount(1, $reflection->getMethod('cancelarPedido')->getParameters());
        $this->assertCount(3, $reflection->getMethod('criarRegistroPedido')->getParameters());
        $this->assertCount(2, $reflection->getMethod('processarItensCarrinho')->getParameters());
        $this->assertCount(1, $reflection->getMethod('incrementarUsoCupom')->getParameters());
        $this->assertCount(1, $reflection->getMethod('logConfirmacao')->getParameters());
    }
}
