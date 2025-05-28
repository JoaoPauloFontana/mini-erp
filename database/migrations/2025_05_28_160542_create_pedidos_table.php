<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();
            $table->string('numero_pedido', 20)->unique();
            $table->string('cliente_nome');
            $table->string('cliente_email');
            $table->string('cliente_telefone')->nullable();
            $table->string('cep', 10);
            $table->text('endereco');
            $table->decimal('subtotal', 10, 2);
            $table->decimal('desconto', 10, 2)->default(0);
            $table->decimal('frete', 10, 2);
            $table->decimal('total', 10, 2);
            $table->foreignId('cupom_id')->nullable()->constrained('cupons');
            $table->enum('status', ['pendente', 'confirmado', 'enviado', 'entregue', 'cancelado'])->default('pendente');
            $table->timestamps();

            $table->index(['numero_pedido', 'status']);
            $table->index(['cliente_email', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};
