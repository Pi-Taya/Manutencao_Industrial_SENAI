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
        Schema::create('lista_manutencaos', function (Blueprint $table) {
            $table->id('id_lista_manutencao');
            $table->foreignId('id_tipo_manutencao')->constrained('tipo_manutencaos', 'id_tipo_manutencao');
            $table->string('descricao_manutencao', 255);
            $table->boolean('status_manutencao')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lista_manutencaos');
    }
};
