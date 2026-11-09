<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrdemServico extends Model
{
    protected $table = 'ordem_servicos';

    protected $primaryKey = 'id_ordem_servico';

    protected $fillable = [
        'id_tipo_manutencao',
        'id_usuario',
        'id_parecer',
        'id_lista_manutencao',
        'id_status_prioridade',
        'descricao_falha',
        'parou_producao',
        'data_abertura',
        'data_previsao',
        'data_inicio_atendimento',
        'data_fechamento',
        'tempo_parada_minutos',
    ];

    protected function casts(): array
    {
        return [
            'parou_producao' => 'boolean',
            'data_abertura' => 'datetime',
            'data_previsao' => 'datetime',
            'data_inicio_atendimento' => 'datetime',
            'data_fechamento' => 'datetime',
            'tempo_parada_minutos' => 'integer',
        ];
    }

    public function tipoManutencao(): BelongsTo
    {
        return $this->belongsTo(TipoManutencao::class, 'id_tipo_manutencao', 'id_tipo_manutencao');
    }

    public function solicitante(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id_usuario');
    }

    public function parecerTecnico(): BelongsTo
    {
        return $this->belongsTo(ParecerTecnico::class, 'id_parecer', 'id_parecer');
    }

    public function statusPrioridade(): BelongsTo
    {
        return $this->belongsTo(StatusPrioridade::class, 'id_status_prioridade', 'id_status_prioridade');
    }

    public function listaManutencao(): BelongsTo
    {
        return $this->belongsTo(ListaManutencao::class, 'id_lista_manutencao', 'id_lista_manutencao');
    }

    public function utilizacoes(): HasMany
    {
        return $this->hasMany(Utiliza::class, 'id_ordem_servico', 'id_ordem_servico');
    }

    public function pecas(): BelongsToMany
    {
        return $this->belongsToMany(
            Peca::class,
            'utilizas',
            'id_ordem_servico',
            'id_peca',
            'id_ordem_servico',
            'id_peca'
        )->withPivot(['id_utiliza', 'quantidade_utilizada'])->withTimestamps();
    }

    public function tecnicos(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'ordem_servico_tecnico',
            'id_ordem_servico',
            'id_usuario',
            'id_ordem_servico',
            'id_usuario'
        )->withTimestamps();
    }
}
