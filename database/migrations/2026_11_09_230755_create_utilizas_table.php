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
        Schema::create('utilizas', function (Blueprint $table) {
            $table->id('id_utiliza');
            $table->foreignId('id_ordem_servico')->constrained('ordem_servicos', 'id_ordem_servico');
            $table->foreignId('id_peca')->constrained('pecas', 'id_peca');
            $table->integer('quantidade_utilizada')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('utilizas');
    }
};
