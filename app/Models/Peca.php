<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Peca extends Model
{
    protected $table = 'pecas';

    protected $primaryKey = 'id_peca';

    protected $fillable = [
        'codigo_item',
        'nome_peca',
        'unidade_medida',
        'quantidade_estoque',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'quantidade_estoque' => 'integer',
            'status' => 'boolean',
        ];
    }

    public function utilizacoes(): HasMany
    {
        return $this->hasMany(Utiliza::class, 'id_peca', 'id_peca');
    }

    public function ordensServico(): BelongsToMany
    {
        return $this->belongsToMany(
            OrdemServico::class,
            'utilizas',
            'id_peca',
            'id_ordem_servico',
            'id_peca',
            'id_ordem_servico'
        )->withPivot(['id_utiliza', 'quantidade_utilizada'])->withTimestamps();
    }

    // --- NOVOS RELACIONAMENTOS PARA STATUS E FICHA TÉCNICA ---

    public function statusPecas(): HasMany
    {
        return $this->hasMany(StatusPeca::class, 'id_peca', 'id_peca');
    }

    public function ultimoStatus(): HasOne
    {
        return $this->hasOne(StatusPeca::class, 'id_peca', 'id_peca')->latestOfMany('id_status_peca');
    }
}