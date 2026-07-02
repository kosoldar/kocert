<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CertLayout extends Model
{
    protected $fillable = [
        'nombre',
        'descripcion',
        'norma_ids',
        'es_default',
        'orientacion',
        'blocks',
        'thumbnail',
    ];

    protected function casts(): array
    {
        return [
            'norma_ids'  => 'array',
            'es_default' => 'boolean',
            'blocks'     => 'array',
        ];
    }

    /**
     * Find the best layout for a given norma.
     * Priority: direct norma_id match → parent norma match → es_default.
     */
    public static function resolveForNorma(Norma $norma): ?self
    {
        if ($layout = static::query()->whereJsonContains('norma_ids', $norma->id)->first()) {
            return $layout;
        }

        if ($norma->parent_norma_id) {
            $layout = static::query()->whereJsonContains('norma_ids', $norma->parent_norma_id)->first();
            if ($layout) {
                return $layout;
            }
        }

        return static::query()->where('es_default', true)->first();
    }
}
