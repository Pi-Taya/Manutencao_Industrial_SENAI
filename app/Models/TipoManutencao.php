<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoManutencao extends Model
{
    protected $table = 'tipo_manutencaos';

    protected $primaryKey = 'id_tipo_manutencao';

    protected $fillable = ['nome_manutencao', 'descricao'];

    public function ordensServico(): HasMany
    {
        return $this->hasMany(OrdemServico::class, 'id_tipo_manutencao', 'id_tipo_manutencao');
    }

    public function listasManutencao(): HasMany
    {
        return $this->hasMany(ListaManutencao::class, 'id_tipo_manutencao', 'id_tipo_manutencao');
    }
}
