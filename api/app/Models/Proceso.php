<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proceso extends Model
{
    protected $fillable = ['nombre', 'descripcion'];

    public function certificados()
    {
        return $this->hasMany(Certificado::class);
    }
}
