<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SoldadorResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'nombre'           => $this->nombre,
            'apellido'         => $this->apellido,
            'nombre_completo'  => $this->nombre_completo,
            'dni'              => $this->dni,
            'cuño'             => $this->cuño,
            'ciudad'           => $this->ciudad,
            'telefono'         => $this->telefono,
            'email'            => $this->email,
            'activo'           => $this->activo,
            'total_certificados' => $this->whenLoaded(
                'certificados',
                fn () => $this->certificados->count()
            ),
            'created_at'       => $this->created_at?->toDateString(),
        ];
    }
}
