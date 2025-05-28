<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $produto1 = \App\Models\Produto::create([
            'nome' => 'Camiseta Básica',
            'preco' => 29.90,
            'descricao' => 'Camiseta 100% algodão'
        ]);

        $produto2 = \App\Models\Produto::create([
            'nome' => 'Calça Jeans',
            'preco' => 89.90,
            'descricao' => 'Calça jeans tradicional'
        ]);

        $produto3 = \App\Models\Produto::create([
            'nome' => 'Tênis Esportivo',
            'preco' => 159.90,
            'descricao' => 'Tênis para corrida e caminhada'
        ]);

        $variacaoP = \App\Models\ProdutoVariacao::create([
            'produto_id' => $produto1->id,
            'nome' => 'P',
            'valor_adicional' => 0
        ]);

        $variacaoM = \App\Models\ProdutoVariacao::create([
            'produto_id' => $produto1->id,
            'nome' => 'M',
            'valor_adicional' => 0
        ]);

        $variacaoG = \App\Models\ProdutoVariacao::create([
            'produto_id' => $produto1->id,
            'nome' => 'G',
            'valor_adicional' => 5
        ]);

        \App\Models\Estoque::create([
            'produto_id' => $produto1->id,
            'variacao_id' => $variacaoP->id,
            'quantidade' => 50
        ]);

        \App\Models\Estoque::create([
            'produto_id' => $produto1->id,
            'variacao_id' => $variacaoM->id,
            'quantidade' => 75
        ]);

        \App\Models\Estoque::create([
            'produto_id' => $produto1->id,
            'variacao_id' => $variacaoG->id,
            'quantidade' => 30
        ]);

        \App\Models\Estoque::create([
            'produto_id' => $produto2->id,
            'variacao_id' => null,
            'quantidade' => 25
        ]);

        \App\Models\Estoque::create([
            'produto_id' => $produto3->id,
            'variacao_id' => null,
            'quantidade' => 15
        ]);

        \App\Models\Cupom::create([
            'codigo' => 'DESCONTO10',
            'tipo' => 'percentual',
            'valor' => 10,
            'valor_minimo' => 50,
            'data_inicio' => now(),
            'data_fim' => now()->addYear(),
            'limite_uso' => 100
        ]);

        \App\Models\Cupom::create([
            'codigo' => 'FRETE15',
            'tipo' => 'valor_fixo',
            'valor' => 15,
            'valor_minimo' => 100,
            'data_inicio' => now(),
            'data_fim' => now()->addYear(),
            'limite_uso' => 50
        ]);
    }
}
