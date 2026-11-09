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
        Schema::create('ordem_servico_tecnico', function (Blueprint $table) {
            $table->foreignId('id_ordem_servico')->constrained('ordem_servicos', 'id_ordem_servico')->cascadeOnDelete();
            $table->foreignId('id_usuario')->constrained('users', 'id_usuario')->cascadeOnDelete();
            $table->primary(['id_ordem_servico', 'id_usuario']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ordem_servico_tecnico');
    }
};
