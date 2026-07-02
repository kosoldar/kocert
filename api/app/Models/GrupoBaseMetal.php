<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GrupoBaseMetal extends Model
{
    protected $table = 'grupos_base_metal';

    protected $fillable = ['norma_id', 'codigo', 'descripcion', 'orden'];

    public function norma(): BelongsTo
    {
        return $this->belongsTo(Norma::class);
    }
}
