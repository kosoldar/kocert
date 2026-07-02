<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Certificado extends Model
{
    protected $fillable = [
        'numero', 'anio', 'revision',
        'soldador_id', 'empresa_id', 'norma_id', 'usuario_id', 'inspector_id',
        'tipo', 'estado', 'resultado',
        'fecha_calificacion', 'fecha_vencimiento',
        'eps_numero', 'pqr_numero',
        'proceso', 'posicion', 'progresion', 'tipo_cupon',
        'variables',
        'joint_design_id', 'joint_detail',
        'pdf_path', 'qr_token', 'observaciones',
        'ranges_calculated_at', 'rules_version',
    ];

    protected function casts(): array
    {
        return [
            'fecha_calificacion' => 'date',
            'fecha_vencimiento'  => 'date',
            'variables'          => 'array',
            'revision'           => 'integer',
        ];
    }

    public function soldador()
    {
        return $this->belongsTo(Soldador::class);
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function norma()
    {
        return $this->belongsTo(Norma::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function inspector()
    {
        return $this->belongsTo(Inspector::class);
    }

    public function jointDesign()
    {
        return $this->belongsTo(JointDesign::class);
    }

    public function tests()
    {
        return $this->hasMany(CertificateTest::class, 'certificado_id');
    }

    public function passes()
    {
        return $this->hasMany(CertificatePasse::class, 'certificado_id')->orderBy('orden');
    }

    public function ranges()
    {
        return $this->hasMany(CertificateRange::class, 'certificado_id');
    }

    public function getCodigoAttribute(): string
    {
        return "RCS{$this->numero}_{$this->anio}";
    }
}
