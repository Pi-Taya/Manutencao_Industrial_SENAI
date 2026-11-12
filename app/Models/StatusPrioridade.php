<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusPrioridade extends Model
{
    use HasFactory;

    protected $table = 'status_prioridades';
    protected $primaryKey = 'id_status_prioridade';

    protected $fillable = [
        'nome_prioridade',
        'tempo_limite_atendimento',
    ];

    public function ordensServico()
    {
        return $this->hasMany(OrdemServico::class, 'id_status_prioridade', 'id_status_prioridade');
    }
}