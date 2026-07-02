<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JointDesign extends Model
{
    protected $fillable = ['code', 'nombre', 'svg', 'parametros'];

    protected function casts(): array
    {
        return ['parametros' => 'array'];
    }
}
