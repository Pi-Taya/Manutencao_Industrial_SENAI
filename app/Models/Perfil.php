<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Perfil extends Model
{
    protected $table = 'perfils';

    protected $primaryKey = 'id_perfil';

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
