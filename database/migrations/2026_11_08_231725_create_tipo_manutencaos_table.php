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
        Schema::create('tipo_manutencaos', function (Blueprint $table) {
            $table->id('id_tipo_manutencao');
            $table->enum('nome_manutencao', ['Preventiva', 'Corretiva', 'Preditiva', 'Inspeção']);
            $table->string('descricao', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tipo_manutencaos');
    }
};
