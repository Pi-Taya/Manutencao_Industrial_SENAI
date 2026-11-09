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
        Schema::create('maquinas', function (Blueprint $table) {
            $table->id('id_maquina');
            $table->foreignId('id_setor')->constrained('setors', 'id_setor');
            $table->foreignId('id_modelo')->constrained('modelos', 'id_modelo');
            $table->foreignId('id_status_maquina')->constrained('status_maquinas', 'id_status_maquina');
            $table->foreignId('id_fabricante')->constrained('fabricantes', 'id_fabricante');
            $table->string('codigo_tag', 50)->unique();
            $table->string('nome_maquina', 100);
            $table->dateTime('data_aquisicao');
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maquinas');
    }
};
