<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QualifiedPosition extends Model
{
    protected $fillable = [
        'norma_id', 'tested_posicion_id', 'qualified_posicion_id', 'joint_type',
    ];

    public function norma()          { return $this->belongsTo(Norma::class); }
    public function testedPosicion() { return $this->belongsTo(Posicion::class, 'tested_posicion_id'); }
    public function qualifiedPosicion() { return $this->belongsTo(Posicion::class, 'qualified_posicion_id'); }
}
