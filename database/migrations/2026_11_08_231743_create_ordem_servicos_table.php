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

            // Chaves Estrangeiras - garanta que id_maquina está no singular
            $table->foreignId('id_maquina')->constrained('maquinas', 'id_maquina');
            $table->foreignId('id_tipo_manutencao')->constrained('tipo_manutencaos', 'id_tipo_manutencao');
            $table->foreignId('id_usuario')->constrained('users', 'id_usuario');
            $table->foreignId('id_status_prioridade')->constrained('status_prioridades', 'id_status_prioridade');
            $table->foreignId('id_parecer')->nullable()->constrained('parecer_tecnicos', 'id_parecer');

            $table->enum('status', [
                'Aberta', 
                'Aguardando Aprovação', 
                'Em Andamento', 
                'Aguardando Peça', 
                'Aguardando Validação', 
                'Concluída', 
                'Cancelada'
            ])->default('Aberta');

            $table->string('descricao_falha', 255);
            $table->boolean('parou_producao')->default(false);

            $table->dateTime('data_abertura')->useCurrent();
            $table->dateTime('data_previsao');
            $table->dateTime('data_inicio_atendimento')->nullable();
            $table->dateTime('data_fechamento')->nullable();
            $table->integer('tempo_parada_minutos')->nullable();

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
