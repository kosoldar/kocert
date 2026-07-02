<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CertificatePasse extends Model
{
    protected $table = 'certificate_passes';

    protected $fillable = [
        'certificado_id', 'orden', 'etiqueta', 'proceso_id',
        'clasificacion_aporte', 'diametro_aporte_mm',
        'polaridad', 'amperaje_min', 'amperaje_max',
        'voltaje_min', 'voltaje_max',
        'velocidad_avance_min', 'velocidad_avance_max',
        'tipo_transferencia', 'progresion',
    ];

    public function certificado() { return $this->belongsTo(Certificado::class); }
    public function proceso()     { return $this->belongsTo(Proceso::class); }
}
