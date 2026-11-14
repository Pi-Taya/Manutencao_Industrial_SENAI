<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parecer_tecnicos', function (Blueprint $table) {
            $table->id('id_parecer');
            
            $table->unsignedBigInteger('id_perfil');
            $table->dateTime('data_registro')->useCurrent();
            $table->text('descricao_laudo');
            $table->enum('condicao_pos_reparo', [
                'Operacional / Aprovado',
                'Operacional com Restrições / Parcial',
                'Aguardando Peça',
                'Condenado / Inoperante'
            ]);

            $table->timestamps();

            $table->foreign('id_perfil')->references('id_perfil')->on('perfis')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parecer_tecnicos');
    }
};