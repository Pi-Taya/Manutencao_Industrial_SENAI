<?php

namespace Database\Seeders;

use App\Models\Fabricante;
use App\Models\Modelo;
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
        // 1. Perfis
        foreach (['Técnico', 'Gestor', 'Administrador'] as $tipoPerfil) {
            Perfil::firstOrCreate(['tipo_perfil' => $tipoPerfil]);
        }

        // 2. Turnos
        foreach (['Manhã', 'Tarde', 'Noite', 'Madrugada'] as $nomeTurno) {
            Turno::firstOrCreate(['nome_turno' => $nomeTurno]);
        }

        // 3. Status de Máquina
        foreach (['Ativa', 'Em Manutenção', 'Inativa'] as $estado) {
            StatusMaquina::firstOrCreate(['estado_da_maquina' => $estado]);
        }

        // 4. Tipos de Manutenção
        foreach (['Preventiva', 'Corretiva', 'Preditiva', 'Inspeção'] as $nomeManutencao) {
            TipoManutencao::firstOrCreate(['nome_manutencao' => $nomeManutencao]);
        }

        // 5. Fabricantes
        foreach (['WEG', 'Siemens', 'Romi', 'Bosch', 'Kuka'] as $nomeFabricante) {
            Fabricante::firstOrCreate(['nome_fabricante' => $nomeFabricante]);
        }

        // 6. Modelos
        $modelos = [
            ['nome_modelo' => 'Torno CNC Galaxy', 'ano_modelo' => 2022],
            ['nome_modelo' => 'Centro de Usinagem Discovery', 'ano_modelo' => 2021],
            ['nome_modelo' => 'Prensa Hidráulica PH-100', 'ano_modelo' => 2020],
            ['nome_modelo' => 'Robô Articulado KR6', 'ano_modelo' => 2023],
        ];
        foreach ($modelos as $modelo) {
            Modelo::firstOrCreate(
                ['nome_modelo' => $modelo['nome_modelo']],
                ['ano_modelo' => $modelo['ano_modelo']]
            );
        }

        // 7. Setores
        $setor = Setor::firstOrCreate(
            ['nome_setor' => 'Manutenção'],
            ['descricao' => 'Setor responsável pela manutenção industrial']
        );
        Setor::firstOrCreate(['nome_setor' => 'Usinagem'], ['descricao' => 'Usinagem de peças']);
        Setor::firstOrCreate(['nome_setor' => 'Montagem'], ['descricao' => 'Linha de montagem']);

        // 8. Busca de Referências para os Usuários
        $turno = Turno::where('nome_turno', 'Manhã')->firstOrFail();
        $perfilGestor = Perfil::where('tipo_perfil', 'Gestor')->firstOrFail();
        $perfilTecnico = Perfil::where('tipo_perfil', 'Técnico')->firstOrFail();

        // 9. Usuários de Teste (campo 'senha' em vez de 'password')
        User::updateOrCreate(
            ['email' => 'gestor@manutencao.test'],
            [
                'id_perfil'     => $perfilGestor->id_perfil,
                'id_turno'      => $turno->id_turno,
                'id_setor'      => $setor->id_setor,
                'nome_usuario'  => 'Gestor de Teste',
                'senha'         => 'password',
                'especialidade' => 'Geral',
                'status'        => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'tecnico@manutencao.test'],
            [
                'id_perfil'     => $perfilTecnico->id_perfil,
                'id_turno'      => $turno->id_turno,
                'id_setor'      => $setor->id_setor,
                'nome_usuario'  => 'Técnico de Teste',
                'senha'         => 'password',
                'especialidade' => 'Mecânica',
                'status'        => true,
            ]
        );
    }
}