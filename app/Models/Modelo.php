<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Modelo extends Model
{
    protected $table = 'modelos';

    protected $primaryKey = 'id_modelo';

    public $timestamps = false;

    protected $fillable = ['nome_modelo', 'ano_modelo'];

    protected function casts(): array
    {
        return ['ano_modelo' => 'integer'];
    }

    public function maquinas(): HasMany
    {
        return $this->hasMany(Maquina::class, 'id_modelo', 'id_modelo');
    }
}
