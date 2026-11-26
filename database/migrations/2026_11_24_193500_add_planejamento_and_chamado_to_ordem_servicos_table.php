<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ordem_servicos', function (Blueprint $table) {
            $table->unsignedBigInteger('id_chamado')->nullable();
            $table->dateTime('data_programada')->nullable();
            $table->text('observacoes_pcm')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('ordem_servicos', function (Blueprint $table) {
            $table->dropColumn([
                'id_chamado',
                'data_programada',
                'observacoes_pcm',
            ]);
        });
    }
};
