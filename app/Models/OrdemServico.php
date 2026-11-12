<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdemServico extends Model
{
    use HasFactory;

    protected $table = 'ordem_servicos';
    protected $primaryKey = 'id_ordem_servico';

    protected $fillable = [
        'id_tipo_manutencao',
        'id_maquina',
        'id_usuario',
        'id_status_prioridade',
        'id_parecer',
        'status',
        'descricao_falha',
        'parou_producao',
        'data_abertura',
        'data_previsao',
        'data_inicio_atendimento',
        'data_fechamento',
        'tempo_parada_minutos',
    ];

    protected $casts = [
        'parou_producao'          => 'boolean',
        'data_abertura'           => 'datetime',
        'data_previsao'           => 'datetime',
        'data_inicio_atendimento' => 'datetime',
        'data_fechamento'         => 'datetime',
    ];

    // Relacionamento com o Solicitante
    public function solicitante()
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id_usuario');
    }

    // Relacionamento com a Máquina
    public function maquina()
    {
        return $this->belongsTo(Maquina::class, 'id_maquina', 'id_maquina');
    }

    // Relacionamento com o Tipo de Manutenção
    public function tipoManutencao()
    {
        return $this->belongsTo(TipoManutencao::class, 'id_tipo_manutencao', 'id_tipo_manutencao');
    }

    // Relacionamento com a Prioridade
    public function prioridade()
    {
        return $this->belongsTo(StatusPrioridade::class, 'id_status_prioridade', 'id_status_prioridade');
    }

    // Relacionamento N:N com os Técnicos Alocados (RF08)
    public function tecnicos()
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