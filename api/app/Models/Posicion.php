<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Posicion extends Model
{
    protected $table = 'posiciones';

    protected $fillable = [
        'codigo', 'descripcion', 'norma', 'tipo', 'es_tuberia',
        'califica_ranura', 'califica_filete',
    ];

    protected $casts = [
        'es_tuberia'      => 'boolean',
        'califica_ranura' => 'array',
        'califica_filete' => 'array',
    ];
}
