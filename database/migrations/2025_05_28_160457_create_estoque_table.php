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
        Schema::create('estoque', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produto_id')->constrained('produtos')->onDelete('cascade');
            $table->foreignId('variacao_id')->nullable()->constrained('produto_variacoes')->onDelete('cascade');
            $table->integer('quantidade')->default(0);
            $table->integer('quantidade_minima')->default(5);
            $table->timestamps();

            $table->unique(['produto_id', 'variacao_id']);
            $table->index(['produto_id', 'quantidade']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estoque');
    }
};
