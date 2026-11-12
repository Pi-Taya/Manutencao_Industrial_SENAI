<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StatusMaquina extends Model
{
    protected $table = 'status_maquinas';

    protected $primaryKey = 'id_status_maquina';

    public $timestamps = false;

    protected $fillable = ['estado_da_maquina'];

    public function maquinas(): HasMany
    {
        return $this->hasMany(Maquina::class, 'id_status_maquina', 'id_status_maquina');
    }
}
