<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
}
