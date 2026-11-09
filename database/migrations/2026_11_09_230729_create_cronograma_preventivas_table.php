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
        Schema::create('cronograma_preventivas', function (Blueprint $table) {
            $table->id('id_cronograma');
            $table->foreignId('id_maquina')->constrained('maquinas', 'id_maquina');
            $table->integer('frequencia_dias');
            $table->dateTime('proxima_data');
            $table->string('descricao_atividade', 255);
            $table->dateTime('data_inicio');
            $table->dateTime('data_fim')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cronograma_preventivas');
    }
};
