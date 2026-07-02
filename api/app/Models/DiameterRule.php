<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiameterRule extends Model
{
    protected $fillable = [
        'norma_id', 'coupon_type',
        'diameter_from_mm', 'diameter_to_mm',
        'qualifies_min_mm', 'qualifies_max_formula', 'notes',
    ];

    public function norma()
    {
        return $this->belongsTo(Norma::class);
    }
}
