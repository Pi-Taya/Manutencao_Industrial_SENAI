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
use Illuminate\Support\Facades\Hash;

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
        $fabRomi = Fabricante::firstOrCreate(['nome_fabricante' => 'Romi']);
        $fabSiemens = Fabricante::firstOrCreate(['nome_fabricante' => 'Siemens']);
        $fabKuka = Fabricante::firstOrCreate(['nome_fabricante' => 'Kuka']);
        Fabricante::firstOrCreate(['nome_fabricante' => 'WEG']);
        Fabricante::firstOrCreate(['nome_fabricante' => 'Bosch']);

        // 6. Modelos (com id_fabricante vinculado)
        $modelos = [
            ['nome_modelo' => 'Torno CNC Galaxy', 'ano_modelo' => 2022, 'id_fabricante' => $fabRomi->id_fabricante],
            ['nome_modelo' => 'Centro de Usinagem Discovery', 'ano_modelo' => 2021, 'id_fabricante' => $fabRomi->id_fabricante],
            ['nome_modelo' => 'Prensa Hidráulica PH-100', 'ano_modelo' => 2020, 'id_fabricante' => $fabSiemens->id_fabricante],
            ['nome_modelo' => 'Robô Articulado KR6', 'ano_modelo' => 2023, 'id_fabricante' => $fabKuka->id_fabricante],
        ];

        foreach ($modelos as $modelo) {
            Modelo::firstOrCreate(
                ['nome_modelo' => $modelo['nome_modelo']],
                [
                    'ano_modelo'    => $modelo['ano_modelo'],
                    'id_fabricante' => $modelo['id_fabricante'],
                ]
            );
        }

        // 7. Setores Industriais Ampliados
        $setores = [
            ['nome_setor' => 'Manutenção', 'descricao' => 'Setor responsável pela manutenção industrial'],
            ['nome_setor' => 'Usinagem', 'descricao' => 'Usinagem de peças e centros CNC'],
            ['nome_setor' => 'Caldeiraria e Solda', 'descricao' => 'Corte, dobra e caldeiraria pesada'],
            ['nome_setor' => 'Estamparia e Injeção', 'descricao' => 'Prensas hidráulicas e injetoras'],
            ['nome_setor' => 'Montagem', 'descricao' => 'Linha final de montagem'],
            ['nome_setor' => 'Utilidades', 'descricao' => 'Central de compressores, caldeiras e chillers'],
            ['nome_setor' => 'Almoxarifado', 'descricao' => 'Estoque de peças e matérias-primas'],
            ['nome_setor' => 'Controle de Qualidade', 'descricao' => 'Laboratório de testes e metrologia'],
        ];

        foreach ($setores as $s) {
            Setor::firstOrCreate(
                ['nome_setor' => $s['nome_setor']],
                ['descricao' => $s['descricao']]
            );
        }

        // 8. Busca de Referências
        $setorManutencao = Setor::where('nome_setor', 'Manutenção')->firstOrFail();
        $turno           = Turno::where('nome_turno', 'Manhã')->firstOrFail();
        $perfilAdmin     = Perfil::where('tipo_perfil', 'Administrador')->firstOrFail();
        $perfilGestor    = Perfil::where('tipo_perfil', 'Gestor')->firstOrFail();
        $perfilTecnico   = Perfil::where('tipo_perfil', 'Técnico')->firstOrFail();

       // 9. Administrador Padrão do Sistema (Único Admin Inicial)
        User::updateOrCreate(
            ['email' => 'admin@manutencao.test'],
            [
                'id_perfil'     => $perfilAdmin->id_perfil,
                'id_turno'      => $turno->id_turno,
                'id_setor'      => $setorManutencao->id_setor,
                'nome_usuario'  => 'Administrador do Sistema',
                'senha'         => Hash::make('password'),
                'especialidade' => 'Geral',
                'status'        => true,
            ]
        );
    }
}