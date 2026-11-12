<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ListaManutencao extends Model
{
    protected $table = 'lista_manutencaos';

    protected $primaryKey = 'id_lista_manutencao';

    protected $fillable = ['id_tipo_manutencao', 'descricao_manutencao', 'status_manutencao'];

    protected function casts(): array
    {
        return ['status_manutencao' => 'boolean'];
    }

    public function tipoManutencao(): BelongsTo
    {
        return $this->belongsTo(TipoManutencao::class, 'id_tipo_manutencao', 'id_tipo_manutencao');
    }

    public function statusPrioridades(): HasMany
    {
        return $this->hasMany(StatusPrioridade::class, 'id_lista_manutencao', 'id_lista_manutencao');
    }

    public function ordensServico(): HasMany
    {
        return $this->hasMany(OrdemServico::class, 'id_lista_manutencao', 'id_lista_manutencao');
    }
}
