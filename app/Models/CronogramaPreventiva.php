<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CronogramaPreventiva extends Model
{
    protected $table = 'cronograma_preventivas';

    protected $primaryKey = 'id_cronograma';

    protected $fillable = [
        'id_maquina',
        'frequencia_dias',
        'proxima_data',
        'descricao_atividade',
        'data_inicio',
        'data_fim',
    ];

    protected function casts(): array
    {
        return [
            'frequencia_dias' => 'integer',
            'proxima_data' => 'datetime',
            'data_inicio' => 'datetime',
            'data_fim' => 'datetime',
        ];
    }

    public function maquina(): BelongsTo
    {
        return $this->belongsTo(Maquina::class, 'id_maquina', 'id_maquina');
    }
}
