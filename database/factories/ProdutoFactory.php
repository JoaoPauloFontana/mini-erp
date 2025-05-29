<?php

namespace Database\Factories;

use App\Models\Produto;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProdutoFactory extends Factory
{
    protected $model = Produto::class;

    public function definition(): array
    {
        $categorias = [
            'Eletrônicos', 'Roupas', 'Casa e Jardim', 'Esportes',
            'Livros', 'Beleza', 'Automotivo', 'Brinquedos'
        ];

        $produtos = [
            'Smartphone', 'Notebook', 'Camiseta', 'Calça Jeans',
            'Mesa de Jantar', 'Sofá', 'Bicicleta', 'Tênis',
            'Livro de Ficção', 'Perfume', 'Pneu', 'Boneca'
        ];

        return [
            'nome' => $this->faker->randomElement($produtos) . ' ' . $this->faker->word(),
            'preco' => $this->faker->randomFloat(2, 10, 1000),
            'descricao' => $this->faker->paragraph(3),
            'ativo' => true,
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => now(),
        ];
    }

    public function ativo(): static
    {
        return $this->state(fn (array $attributes) => [
            'ativo' => true,
        ]);
    }

    public function inativo(): static
    {
        return $this->state(fn (array $attributes) => [
            'ativo' => false,
        ]);
    }

    public function comPreco(float $preco): static
    {
        return $this->state(fn (array $attributes) => [
            'preco' => $preco,
        ]);
    }

    public function barato(): static
    {
        return $this->state(fn (array $attributes) => [
            'preco' => $this->faker->randomFloat(2, 10, 50),
        ]);
    }

    public function caro(): static
    {
        return $this->state(fn (array $attributes) => [
            'preco' => $this->faker->randomFloat(2, 500, 2000),
        ]);
    }

    public function eletronico(): static
    {
        $produtos = ['Smartphone', 'Notebook', 'Tablet', 'Smart TV', 'Fone de Ouvido'];

        return $this->state(fn (array $attributes) => [
            'nome' => $this->faker->randomElement($produtos) . ' ' . $this->faker->company(),
            'preco' => $this->faker->randomFloat(2, 200, 3000),
            'descricao' => 'Produto eletrônico de alta qualidade com tecnologia avançada.',
        ]);
    }

    public function vestuario(): static
    {
        $produtos = ['Camiseta', 'Calça', 'Vestido', 'Jaqueta', 'Shorts'];
        $tamanhos = ['P', 'M', 'G', 'GG'];

        return $this->state(fn (array $attributes) => [
            'nome' => $this->faker->randomElement($produtos) . ' Tamanho ' . $this->faker->randomElement($tamanhos),
            'preco' => $this->faker->randomFloat(2, 30, 200),
            'descricao' => 'Peça de vestuário confortável e estilosa.',
        ]);
    }
}
