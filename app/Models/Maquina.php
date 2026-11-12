<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Maquina extends Model
{
    protected $table = 'maquinas';

    protected $primaryKey = 'id_maquina';

    protected $fillable = [
        'id_setor',
        'id_modelo',
        'id_status_maquina',
        'id_fabricante',
        'codigo_tag',
        'nome_maquina',
        'data_aquisicao',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'data_aquisicao' => 'datetime',
            'status' => 'boolean',
        ];
    }

    public function setor(): BelongsTo
    {
        return $this->belongsTo(Setor::class, 'id_setor', 'id_setor');
    }

    public function modelo(): BelongsTo
    {
        return $this->belongsTo(Modelo::class, 'id_modelo', 'id_modelo');
    }

    public function fabricante(): BelongsTo
    {
        return $this->belongsTo(Fabricante::class, 'id_fabricante', 'id_fabricante');
    }

    public function statusMaquina(): BelongsTo
    {
        return $this->belongsTo(StatusMaquina::class, 'id_status_maquina', 'id_status_maquina');
    }

    public function cronogramasPreventiva(): HasMany
    {
        return $this->hasMany(CronogramaPreventiva::class, 'id_maquina', 'id_maquina');
    }
}
