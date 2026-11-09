<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Setor extends Model
{
    protected $table = 'setors';

    protected $primaryKey = 'id_setor';

    protected $fillable = ['nome_setor', 'descricao'];

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'id_setor', 'id_setor');
    }

    public function maquinas(): HasMany
    {
        return $this->hasMany(Maquina::class, 'id_setor', 'id_setor');
    }
}
