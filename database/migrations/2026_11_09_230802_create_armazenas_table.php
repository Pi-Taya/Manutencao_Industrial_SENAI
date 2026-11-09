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
        Schema::create('armazenas', function (Blueprint $table) {
            $table->id('id_armazena');
            $table->foreignId('id_ordem_servico')->constrained('ordem_servicos', 'id_ordem_servico');
            $table->foreignId('id_historico')->constrained('historico_servicos', 'id_historico');
            $table->foreignId('id_maquina')->constrained('maquinas', 'id_maquina');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('armazenas');
    }
};
