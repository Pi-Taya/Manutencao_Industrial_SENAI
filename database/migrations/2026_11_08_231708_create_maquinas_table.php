<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maquinas', function (Blueprint $table) {
            $table->id('id_maquina');
            
            // Colunas de FK (Unsigned BigInteger compatível com $table->id())
            $table->unsignedBigInteger('id_setor');
            $table->unsignedBigInteger('id_modelo');
            $table->unsignedBigInteger('id_fabricante');
            $table->unsignedBigInteger('id_status_maquina');

            // Campos da Máquina (RF04)
            $table->string('codigo_tag', 50)->unique();
            $table->string('nome_maquina', 100);
            $table->dateTime('data_aquisicao');
            $table->boolean('status')->default(true);
            $table->timestamps();

            // Declaração das Constraints
            $table->foreign('id_setor')->references('id_setor')->on('setores')->onDelete('cascade');
            $table->foreign('id_modelo')->references('id_modelo')->on('modelos')->onDelete('cascade');
            $table->foreign('id_fabricante')->references('id_fabricante')->on('fabricantes')->onDelete('cascade');
            $table->foreign('id_status_maquina')->references('id_status_maquina')->on('status_maquinas')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maquinas');
    }
};