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
        Schema::create('historico_servicos', function (Blueprint $table) {
            $table->id('id_historico');
            $table->foreignId('id_ordem_servico')->constrained('ordem_servicos', 'id_ordem_servico');
            $table->dateTime('data_modificacao')->useCurrent();
            $table->enum('status_anterior', ['Aberta', 'Aguardando Aprovação', 'Em Andamento', 'Aguardando Peça', 'Aguardando Validação', 'Concluída', 'Cancelada'])->nullable();
            $table->enum('status_atual', ['Aberta', 'Aguardando Aprovação', 'Em Andamento', 'Aguardando Peça', 'Aguardando Validação', 'Concluída', 'Cancelada']);
            $table->string('observacao', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historico_servicos');
    }
};
