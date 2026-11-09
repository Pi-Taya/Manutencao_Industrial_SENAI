<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StatusPrioridade extends Model
{
    protected $table = 'status_prioridades';

    protected $primaryKey = 'id_status_prioridade';

    protected $fillable = ['id_lista_manutencao', 'nome_prioridade', 'tempo_limite_atendimento'];

    public function listaManutencao(): BelongsTo
    {
        return $this->belongsTo(ListaManutencao::class, 'id_lista_manutencao', 'id_lista_manutencao');
    }

    public function ordensServico(): HasMany
    {
        return $this->hasMany(OrdemServico::class, 'id_status_prioridade', 'id_status_prioridade');
    }
}
