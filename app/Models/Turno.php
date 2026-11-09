<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Turno extends Model
{
    protected $table = 'turnos';

    protected $primaryKey = 'id_turno';

    protected $fillable = ['nome_turno'];

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'id_turno', 'id_turno');
    }
}
