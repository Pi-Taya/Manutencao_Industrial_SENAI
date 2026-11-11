<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;


class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';

    protected $primaryKey = 'id_usuario';

    protected $fillable = [
        'id_perfil',
        'id_turno',
        'id_setor',
        'nome_usuario',
        'email',
        'senha',
        'especialidade',
        'status',
    ];

    protected $hidden = [
        'senha',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'senha' => 'hashed',
            'status' => 'boolean',
        ];
    }

    /**
     * Mapeia o campo de senha da autenticação para 'senha'
     */
    public function getAuthPasswordName(): string
    {
        return 'senha';
    }

    public function perfil()
    {
        return $this->belongsTo(Perfil::class, 'id_perfil', 'id_perfil');
    }

    public function turno()
    {
        return $this->belongsTo(Turno::class, 'id_turno', 'id_turno');
    }

    public function setor()
    {
        return $this->belongsTo(Setor::class, 'id_setor', 'id_setor');
    }

    public function ordensSolicitadas()
    {
        return $this->hasMany(OrdemServico::class, 'id_usuario', 'id_usuario');
    }

    public function ordensComoTecnico()
    {
        return $this->belongsToMany(
            OrdemServico::class,
            'ordem_servico_tecnico',
            'id_usuario',
            'id_ordem_servico',
            'id_usuario',
            'id_ordem_servico'
        )->withTimestamps();
    }
}