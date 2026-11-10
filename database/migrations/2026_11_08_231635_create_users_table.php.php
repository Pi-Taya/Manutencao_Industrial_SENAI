<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id('id_usuario');
            
            // Relacionamentos com o nome correto das tabelas no plural em português
            $table->unsignedBigInteger('id_perfil');
            $table->unsignedBigInteger('id_turno');
            $table->unsignedBigInteger('id_setor');

            $table->string('nome_usuario', 100);
            $table->string('email', 100)->unique();
            $table->string('senha');
            $table->enum('especialidade', ['Elétrica', 'Mecânica', 'Hidráulica', 'Geral'])->default('Geral');
            $table->boolean('status')->default(true);
            $table->rememberToken();
            $table->timestamps();

            // Constraints corrigidas apontando para 'setores'
            $table->foreign('id_perfil')->references('id_perfil')->on('perfis')->onDelete('cascade');
            $table->foreign('id_turno')->references('id_turno')->on('turnos')->onDelete('cascade');
            $table->foreign('id_setor')->references('id_setor')->on('setores')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};