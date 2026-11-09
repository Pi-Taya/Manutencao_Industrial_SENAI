<?php

namespace Database\Factories;

use App\Models\Perfil;
use App\Models\Setor;
use App\Models\Turno;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_perfil' => Perfil::firstOrCreate(['tipo_perfil' => 'Técnico'])->id_perfil,
            'id_turno' => Turno::firstOrCreate(['nome_turno' => 'Manhã'])->id_turno,
            'id_setor' => Setor::firstOrCreate(['nome_setor' => 'Manutenção'])->id_setor,
            'nome_usuario' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => 'password',
            'especialidade' => fake()->randomElement(['Elétrica', 'Mecânica', 'Hidráulica', 'Geral']),
            'status' => true,
        ];
    }
}
