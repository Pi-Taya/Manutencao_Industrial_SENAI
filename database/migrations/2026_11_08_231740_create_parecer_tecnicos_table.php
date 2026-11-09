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
        Schema::create('parecer_tecnicos', function (Blueprint $table) {
            $table->id('id_parecer');
            $table->foreignId('id_perfil')->constrained('perfils', 'id_perfil');
            $table->dateTime('data_registro')->useCurrent();
            $table->string('descricao_laudo', 255);
            $table->enum('condicao_pos_reparo', [
                'Operacional / Aprovado', 
                'Operacional com Restrições / Parcial', 
                'Aguardando Peça', 
                'Condenado / Inoperante'
            ]);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parecer_tecnicos');
    }
};
