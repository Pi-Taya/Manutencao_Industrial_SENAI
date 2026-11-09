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
        Schema::create('ordem_servicos', function (Blueprint $table) {
            $table->id('id_ordem_servico');
            $table->foreignId('id_tipo_manutencao')->constrained('tipo_manutencaos', 'id_tipo_manutencao');
            $table->foreignId('id_usuario')->constrained('users', 'id_usuario'); // Solicitante da OS
            $table->foreignId('id_parecer')->nullable()->constrained('parecer_tecnicos', 'id_parecer');
            $table->foreignId('id_lista_manutencao')->nullable()->constrained('lista_manutencaos', 'id_lista_manutencao');
            $table->foreignId('id_status_prioridade')->constrained('status_prioridades', 'id_status_prioridade');
            $table->string('descricao_falha', 255);
            $table->boolean('parou_producao')->default(false); // RN04
            $table->dateTime('data_abertura')->useCurrent();
            $table->dateTime('data_previsao');
            $table->dateTime('data_inicio_atendimento')->nullable(); // Para MTTR
            $table->dateTime('data_fechamento')->nullable(); // Para MTTR
            $table->integer('tempo_parada_minutos')->nullable(); // Para MTTR
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ordem_servicos');
    }
};
