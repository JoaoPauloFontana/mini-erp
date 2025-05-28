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
        Schema::create('cupons', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 50)->unique();
            $table->enum('tipo', ['percentual', 'valor_fixo']);
            $table->decimal('valor', 10, 2);
            $table->decimal('valor_minimo', 10, 2)->default(0);
            $table->date('data_inicio');
            $table->date('data_fim');
            $table->integer('limite_uso')->nullable();
            $table->integer('usado')->default(0);
            $table->boolean('ativo')->default(true);
            $table->timestamps();

            $table->index(['codigo', 'ativo']);
            $table->index(['data_inicio', 'data_fim']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cupons');
    }
};
