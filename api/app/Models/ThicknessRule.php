<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ThicknessRule extends Model
{
    protected $fillable = [
        'norma_id', 'coupon_type',
        'thickness_from_mm', 'thickness_to_mm', 'min_layers',
        'qualifies_min_mm', 'qualifies_max_formula', 'notes',
    ];

    public function norma()
    {
        return $this->belongsTo(Norma::class);
    }
}
