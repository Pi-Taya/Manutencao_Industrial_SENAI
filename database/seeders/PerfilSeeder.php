<?php

namespace Database\Seeders;

use App\Models\Perfil;
use Illuminate\Database\Seeder;

class PerfilSeeder extends Seeder
{
    public function run(): void
    {
        foreach (Perfil::TIPOS as $tipoPerfil) {
            Perfil::firstOrCreate(['tipo_perfil' => $tipoPerfil]);
        }
    }
}
