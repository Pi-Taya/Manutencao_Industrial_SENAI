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
        Schema::create('status_prioridades', function (Blueprint $table) {
            $table->id('id_status_prioridade');
            $table->foreignId('id_lista_manutencao')->nullable()->constrained('lista_manutencaos', 'id_lista_manutencao');
            $table->enum('nome_prioridade', ['Baixa', 'Média', 'Alta', 'Crítica'])->default('Média');
            $table->time('tempo_limite_atendimento');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('status_prioridades');
    }
};
