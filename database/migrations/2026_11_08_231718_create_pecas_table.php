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
        Schema::create('pecas', function (Blueprint $table) {
            $table->id('id_peca');
            $table->string('codigo_item', 50)->unique();
            $table->string('nome_peca', 100);
            $table->enum('unidade_medida', ['UN', 'KG', 'L', 'M', 'CX', 'PCT'])->default('UN');
            $table->integer('quantidade_estoque')->default(0);
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pecas');
    }
};
