<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Modelo extends Model
{
    protected $table = 'modelos';

    protected $primaryKey = 'id_modelo';

    public $timestamps = false;

    protected $fillable = [
        'nome_modelo', 
        'id_fabricante', 
        'especificacoes_tecnicas',
        'ano_modelo'
    ];

    protected function casts(): array
    {
        return ['ano_modelo' => 'integer'];
    }

    /**
     * Relacionamento: Um Modelo pertence a um Fabricante
     */
    public function fabricante(): BelongsTo
    {
        return $this->belongsTo(Fabricante::class, 'id_fabricante', 'id_fabricante');
    }

    /**
     * Relacionamento: Um Modelo possui muitas Máquinas
     */
    public function maquinas(): HasMany
    {
        return $this->hasMany(Maquina::class, 'id_modelo', 'id_modelo');
    }
}