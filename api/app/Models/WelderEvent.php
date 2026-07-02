<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WelderEvent extends Model
{
    protected $table = 'welder_events';

    protected $fillable = [
        'soldador_id', 'event_type', 'event_date', 'notas', 'created_by',
    ];

    protected function casts(): array
    {
        return ['event_date' => 'date'];
    }

    public function soldador()   { return $this->belongsTo(Soldador::class); }
    public function createdBy()  { return $this->belongsTo(User::class, 'created_by'); }
}
