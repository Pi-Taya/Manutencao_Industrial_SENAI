<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('maquinas', function (Blueprint $table) {
            $table->unsignedTinyInteger('criticidade')
                ->default(3)
                ->comment('Escala de criticidade de 1 (muito baixa) a 5 (crítica).')
                ->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('maquinas', function (Blueprint $table) {
            $table->dropColumn('criticidade');
        });
    }
};
