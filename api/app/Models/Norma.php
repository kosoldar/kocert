<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Norma extends Model
{
    protected $fillable = ['nombre', 'descripcion', 'parent_norma_id'];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Norma::class, 'parent_norma_id');
    }

    public function certificados(): HasMany
    {
        return $this->hasMany(Certificado::class);
    }

    public function gruposBaseMetal(): HasMany
    {
        return $this->hasMany(GrupoBaseMetal::class);
    }

    public function gruposConsumible(): HasMany
    {
        return $this->hasMany(GrupoConsumible::class);
    }
}
