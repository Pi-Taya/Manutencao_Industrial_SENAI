<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ParecerTecnico extends Model
{
    protected $table = 'parecer_tecnicos';

    protected $primaryKey = 'id_parecer';

    protected $fillable = [
        'id_perfil',
        'data_registro',
        'descricao_laudo',
        'condicao_pos_reparo',
    ];

    protected function casts(): array
    {
        return ['data_registro' => 'datetime'];
    }

    public function perfil(): BelongsTo
    {
        return $this->belongsTo(Perfil::class, 'id_perfil', 'id_perfil');
    }

    public function ordemServico(): HasOne
    {
        return $this->hasOne(OrdemServico::class, 'id_parecer', 'id_parecer');
    }
}
