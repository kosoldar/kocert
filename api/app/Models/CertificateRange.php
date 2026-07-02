<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CertificateRange extends Model
{
    protected $table = 'certificate_ranges';

    protected $fillable = [
        'certificado_id', 'type', 'descripcion', 'min_value', 'max_value',
    ];

    public function certificado() { return $this->belongsTo(Certificado::class); }
}
