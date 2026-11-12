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
        Schema::create('modelos', function (Blueprint $table) {
            $table->id('id_modelo');
            $table->string('nome_modelo', 100);
            $table->foreignId('id_fabricante')->constrained('fabricantes', 'id_fabricante')->onDelete('cascade');
            $table->text('especificacoes_tecnicas')->nullable();
            $table->integer('ano_modelo')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modelos');
    }
};