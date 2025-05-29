<?php

namespace Database\Factories;

use App\Models\PedidoItem;
use App\Models\Pedido;
use App\Models\Produto;
use Illuminate\Database\Eloquent\Factories\Factory;

class PedidoItemFactory extends Factory
{
    protected $model = PedidoItem::class;

    public function definition(): array
    {
        $quantidade = $this->faker->numberBetween(1, 5);
        $precoUnitario = $this->faker->randomFloat(2, 10, 200);
        $subtotal = $quantidade * $precoUnitario;

        return [
            'pedido_id' => Pedido::factory(),
            'produto_id' => Produto::factory(),
            'variacao_id' => null,
            'quantidade' => $quantidade,
            'preco_unitario' => $precoUnitario,
            'subtotal' => $subtotal,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function paraPedido(int $pedidoId): static
    {
        return $this->state(fn (array $attributes) => [
            'pedido_id' => $pedidoId,
        ]);
    }

    public function comProduto(int $produtoId): static
    {
        return $this->state(fn (array $attributes) => [
            'produto_id' => $produtoId,
        ]);
    }

    public function comQuantidade(int $quantidade): static
    {
        return $this->state(function (array $attributes) use ($quantidade) {
            $precoUnitario = $attributes['preco_unitario'] ?? 50.00;

            return [
                'quantidade' => $quantidade,
                'subtotal' => $quantidade * $precoUnitario,
            ];
        });
    }

    public function comPreco(float $preco): static
    {
        return $this->state(function (array $attributes) use ($preco) {
            $quantidade = $attributes['quantidade'] ?? 1;

            return [
                'preco_unitario' => $preco,
                'subtotal' => $quantidade * $preco,
            ];
        });
    }

    public function comVariacao(int $variacaoId): static
    {
        return $this->state(fn (array $attributes) => [
            'variacao_id' => $variacaoId,
        ]);
    }

    public function unico(): static
    {
        return $this->state(function (array $attributes) {
            $precoUnitario = $attributes['preco_unitario'] ?? 50.00;

            return [
                'quantidade' => 1,
                'subtotal' => $precoUnitario,
            ];
        });
    }

    public function multiplo(): static
    {
        return $this->state(function (array $attributes) {
            $quantidade = $this->faker->numberBetween(3, 10);
            $precoUnitario = $attributes['preco_unitario'] ?? 50.00;

            return [
                'quantidade' => $quantidade,
                'subtotal' => $quantidade * $precoUnitario,
            ];
        });
    }
}
