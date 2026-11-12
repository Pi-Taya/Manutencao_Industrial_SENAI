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
        Schema::create('status_maquinas', function (Blueprint $table) {
            $table->id('id_status_maquina');
            $table->string('estado_da_maquina', 50); // Ex: Ativa, Em Manutenção, Inativa
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('status_maquinas');
    }
};
