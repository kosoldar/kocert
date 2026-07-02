<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestType extends Model
{
    protected $fillable = ['norma_id', 'code', 'nombre', 'requerido'];

    protected function casts(): array
    {
        return ['requerido' => 'boolean'];
    }

    public function norma() { return $this->belongsTo(Norma::class); }
}
