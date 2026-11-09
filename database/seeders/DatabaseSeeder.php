<?php

namespace Database\Seeders;

use App\Models\Perfil;
use App\Models\Setor;
use App\Models\StatusMaquina;
use App\Models\TipoManutencao;
use App\Models\Turno;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        foreach (['Técnico', 'Gestor', 'Administrador'] as $tipoPerfil) {
            Perfil::firstOrCreate(['tipo_perfil' => $tipoPerfil]);
        }

        foreach (['Manhã', 'Tarde', 'Noite', 'Madrugada'] as $nomeTurno) {
            Turno::firstOrCreate(['nome_turno' => $nomeTurno]);
        }

        foreach (['Ativa', 'Em Manutenção', 'Inativa'] as $estado) {
            StatusMaquina::firstOrCreate(['estado_da_maquina' => $estado]);
        }

        foreach (['Preventiva', 'Corretiva', 'Preditiva', 'Inspeção'] as $nomeManutencao) {
            TipoManutencao::firstOrCreate(['nome_manutencao' => $nomeManutencao]);
        }

        $setor = Setor::firstOrCreate(
            ['nome_setor' => 'Manutenção'],
            ['descricao' => 'Setor responsável pela manutenção industrial']
        );
        $turno = Turno::where('nome_turno', 'Manhã')->firstOrFail();
        $perfilGestor = Perfil::where('tipo_perfil', 'Gestor')->firstOrFail();
        $perfilTecnico = Perfil::where('tipo_perfil', 'Técnico')->firstOrFail();

        User::updateOrCreate(
            ['email' => 'gestor@manutencao.test'],
            [
                'id_perfil' => $perfilGestor->id_perfil,
                'id_turno' => $turno->id_turno,
                'id_setor' => $setor->id_setor,
                'nome_usuario' => 'Gestor de Teste',
                'password' => 'password',
                'especialidade' => 'Geral',
                'status' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'tecnico@manutencao.test'],
            [
                'id_perfil' => $perfilTecnico->id_perfil,
                'id_turno' => $turno->id_turno,
                'id_setor' => $setor->id_setor,
                'nome_usuario' => 'Técnico de Teste',
                'password' => 'password',
                'especialidade' => 'Mecânica',
                'status' => true,
            ]
        );
    }
}
