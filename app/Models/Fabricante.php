<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Fabricante extends Model
{
    protected $table = 'fabricantes';

    protected $primaryKey = 'id_fabricante';

    protected $fillable = ['nome_fabricante'];

    public function maquinas(): HasMany
    {
        return $this->hasMany(Maquina::class, 'id_fabricante', 'id_fabricante');
    }
}
