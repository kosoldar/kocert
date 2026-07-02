<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Consumible extends Model
{
    protected $table = 'consumibles';

    protected $fillable = ['grupo_id', 'clasificacion', 'sfa', 'proceso', 'descripcion'];

    public function grupo(): BelongsTo
    {
        return $this->belongsTo(GrupoConsumible::class, 'grupo_id');
    }
}
