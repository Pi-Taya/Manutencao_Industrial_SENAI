<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Perfil extends Model
{
    protected $table = 'perfis'; // <- Corrigido de 'perfils' para 'perfis'

    protected $primaryKey = 'id_perfil';

    public $timestamps = false; // <- Adicionado para alinhar com o SQL sem created_at/updated_at

    protected $fillable = ['tipo_perfil'];

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'id_perfil', 'id_perfil');
    }

    public function parecerTecnicos(): HasMany
    {
        return $this->hasMany(ParecerTecnico::class, 'id_perfil', 'id_perfil');
    }
}