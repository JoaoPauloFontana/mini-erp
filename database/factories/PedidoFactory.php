<?php

namespace Database\Factories;

use App\Models\Pedido;
use Illuminate\Database\Eloquent\Factories\Factory;

class PedidoFactory extends Factory
{
    protected $model = Pedido::class;

    public function definition(): array
    {
        $subtotal = $this->faker->randomFloat(2, 50, 500);
        $desconto = $this->faker->randomFloat(2, 0, $subtotal * 0.2);
        $frete = $this->faker->randomElement([0, 15, 20]);
        $total = $subtotal - $desconto + $frete;

        return [
            'numero_pedido' => $this->faker->unique()->numerify('PED-######'),
            'cliente_nome' => $this->faker->name(),
            'cliente_email' => $this->faker->safeEmail(),
            'cliente_telefone' => $this->faker->phoneNumber(),
            'cep' => $this->faker->numerify('########'),
            'endereco' => $this->faker->streetAddress() . ', ' .
                        $this->faker->buildingNumber() . ', ' .
                        $this->faker->city() . ' - ' .
                        $this->faker->stateAbbr(),
            'subtotal' => $subtotal,
            'desconto' => $desconto,
            'frete' => $frete,
            'total' => $total,
            'cupom_id' => null,
            'status' => $this->faker->randomElement(['pendente', 'confirmado', 'enviado', 'entregue']),
            'created_at' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'updated_at' => now(),
        ];
    }

    public function pendente(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pendente',
        ]);
    }

    public function confirmado(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'confirmado',
        ]);
    }

    public function enviado(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'enviado',
        ]);
    }

    public function entregue(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'entregue',
        ]);
    }

    public function cancelado(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelado',
        ]);
    }

    public function freteGratis(): static
    {
        return $this->state(function (array $attributes) {
            $subtotal = 250.00;
            $desconto = $attributes['desconto'] ?? 0;

            return [
                'subtotal' => $subtotal,
                'frete' => 0.00,
                'total' => $subtotal - $desconto,
            ];
        });
    }

    public function comDesconto(): static
    {
        return $this->state(function (array $attributes) {
            $subtotal = $attributes['subtotal'] ?? 100.00;
            $desconto = $subtotal * 0.1;
            $frete = $attributes['frete'] ?? 15.00;

            return [
                'desconto' => $desconto,
                'total' => $subtotal - $desconto + $frete,
            ];
        });
    }

    public function semEmail(): static
    {
        return $this->state(fn (array $attributes) => [
            'cliente_email' => '',
        ]);
    }

    public function comEmail(string $email): static
    {
        return $this->state(fn (array $attributes) => [
            'cliente_email' => $email,
        ]);
    }
}
