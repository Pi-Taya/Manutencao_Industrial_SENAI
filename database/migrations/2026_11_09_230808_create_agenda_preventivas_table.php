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
        Schema::create('agenda_preventivas', function (Blueprint $table) {
            $table->id('id_agenda_preventiva');
            $table->foreignId('id_cronograma')->constrained('cronograma_preventivas', 'id_cronograma');
            $table->foreignId('id_status_agendamento')->constrained('status_agendamentos', 'id_status_agendamento');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agenda_preventivas');
    }
};
