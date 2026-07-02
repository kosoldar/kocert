<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inspector extends Model
{
    protected $fillable = [
        'nombre', 'certificacion', 'firma_path',
        'email', 'telefono', 'activo',
    ];

    protected function casts(): array
    {
        return ['activo' => 'boolean'];
    }

    public function certificados()
    {
        return $this->hasMany(Certificado::class);
    }
}
