<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CertificateTest extends Model
{
    protected $table = 'certificate_tests';

    protected $fillable = [
        'certificado_id', 'test_type_id', 'resultado', 'notas', 'detalles',
    ];

    protected function casts(): array
    {
        return ['detalles' => 'array'];
    }

    public function certificado() { return $this->belongsTo(Certificado::class); }
    public function testType()    { return $this->belongsTo(TestType::class); }
}
