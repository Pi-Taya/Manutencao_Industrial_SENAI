<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StatusPeca extends Model
{
    protected $table = 'status_pecas';

    protected $primaryKey = 'id_status_peca';

    protected $fillable = [
        'id_peca',
        'descricao_status_peca',
    ];

    public function peca(): BelongsTo
    {
        return $this->belongsTo(Peca::class, 'id_peca', 'id_peca');
    }
}