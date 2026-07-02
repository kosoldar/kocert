<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Soldador extends Model
{
    protected $table = 'soldadores';

    protected $fillable = [
        'nombre', 'apellido', 'dni', 'fecha_nacimiento', 'nacionalidad',
        'cuño', 'foto_path', 'ciudad', 'telefono', 'email', 'activo',
    ];

    protected $appends = ['nombre_completo'];

    protected function casts(): array
    {
        return ['activo' => 'boolean'];
    }

    public function certificados()
    {
        return $this->hasMany(Certificado::class);
    }

    public function getNombreCompletoAttribute(): string
    {
        return "{$this->apellido}, {$this->nombre}";
    }
}
