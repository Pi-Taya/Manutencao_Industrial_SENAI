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
        Schema::create('status_pecas', function (Blueprint $table) {
            $table->id('id_status_peca');
            $table->foreignId('id_peca')->constrained('pecas', 'id_peca');
            $table->string('descricao_status_peca', 200);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('status_pecas');
    }
};
