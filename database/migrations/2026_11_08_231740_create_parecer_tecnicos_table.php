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
            
            // Chaves Estrangeiras
            $table->unsignedBigInteger('id_perfil');
            // adicione outras FKs da tabela aqui se houver (ex: id_maquina, id_os, etc)

            $table->text('descricao_parecer');
            $table->timestamps();

            // Constraint apontando para a tabela 'perfis'
            $table->foreign('id_perfil')->references('id_perfil')->on('perfis')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parecer_tecnicos');
    }
};