<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    protected $fillable = ['nombre', 'cuit', 'contacto', 'activo'];

    protected function casts(): array
    {
        return ['activo' => 'boolean'];
    }

    public function certificados()
    {
        return $this->hasMany(Certificado::class);
    }
}
