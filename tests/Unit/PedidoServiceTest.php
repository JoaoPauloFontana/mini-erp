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
        $this->assertTrue(method_exists($this->pedidoService, 'criarPedido'));
        $this->assertTrue(method_exists($this->pedidoService, 'atualizarStatus'));
        $this->assertTrue(method_exists($this->pedidoService, 'cancelarPedido'));
        $this->assertTrue(method_exists($this->pedidoService, 'criarRegistroPedido'));
        $this->assertTrue(method_exists($this->pedidoService, 'processarItensCarrinho'));
        $this->assertTrue(method_exists($this->pedidoService, 'incrementarUsoCupom'));
        $this->assertTrue(method_exists($this->pedidoService, 'logConfirmacao'));

        $this->assertInstanceOf(EstoqueService::class, $this->estoqueService);
        $this->assertInstanceOf(CarrinhoService::class, $this->carrinhoService);

        $this->assertInstanceOf(PedidoService::class, $this->pedidoService);
    }

    public function test_service_tem_dependencias_corretas()
    {
        $reflection = new \ReflectionClass($this->pedidoService);
        $constructor = $reflection->getConstructor();
        $parameters = $constructor->getParameters();

        $this->assertCount(2, $parameters);
        $this->assertEquals('estoqueService', $parameters[0]->getName());
        $this->assertEquals('carrinhoService', $parameters[1]->getName());
        $this->assertEquals(EstoqueService::class, $parameters[0]->getType()->getName());
        $this->assertEquals(CarrinhoService::class, $parameters[1]->getType()->getName());
    }

    public function test_service_metodos_publicos_e_privados()
    {
        $reflection = new \ReflectionClass($this->pedidoService);

        $this->assertTrue($reflection->getMethod('criarPedido')->isPublic());
        $this->assertTrue($reflection->getMethod('atualizarStatus')->isPublic());
        $this->assertTrue($reflection->getMethod('cancelarPedido')->isPublic());

        $this->assertTrue($reflection->getMethod('criarRegistroPedido')->isPrivate());
        $this->assertTrue($reflection->getMethod('processarItensCarrinho')->isPrivate());
        $this->assertTrue($reflection->getMethod('incrementarUsoCupom')->isPrivate());
        $this->assertTrue($reflection->getMethod('logConfirmacao')->isPrivate());
    }

    public function test_metodos_dependem_de_models_testados_indiretamente()
    {
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
