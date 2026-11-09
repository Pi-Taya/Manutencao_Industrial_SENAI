<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Utiliza extends Model
{
    protected $table = 'utilizas';

    protected $primaryKey = 'id_utiliza';

    protected $fillable = [
        'id_ordem_servico',
        'id_peca',
        'quantidade_utilizada',
    ];

    protected function casts(): array
    {
        return ['quantidade_utilizada' => 'integer'];
    }

    public function ordemServico(): BelongsTo
    {
        return $this->belongsTo(OrdemServico::class, 'id_ordem_servico', 'id_ordem_servico');
    }

    public function peca(): BelongsTo
    {
        return $this->belongsTo(Peca::class, 'id_peca', 'id_peca');
    }
}
