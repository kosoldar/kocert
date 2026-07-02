<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GrupoConsumible extends Model
{
    protected $table = 'grupos_consumible';

    protected $fillable = ['norma_id', 'codigo', 'descripcion', 'orden'];

    public function norma(): BelongsTo
    {
        return $this->belongsTo(Norma::class);
    }

    public function consumibles(): HasMany
    {
        return $this->hasMany(Consumible::class, 'grupo_id');
    }
}
