<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSoldadorRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'nombre'           => 'required|string|max:100',
            'apellido'         => 'required|string|max:100',
            'dni'              => 'required|string|max:20|unique:soldadores,dni',
            'fecha_nacimiento' => 'nullable|date',
            'nacionalidad'     => 'nullable|string|max:100',
            'cuño'             => 'nullable|string|max:20',
            'ciudad'   => 'nullable|string|max:100',
            'telefono' => 'nullable|string|max:30',
            'email'    => 'nullable|email|max:100',
        ];
    }

    public function attributes(): array
    {
        return [
            'nombre'   => 'nombre',
            'apellido' => 'apellido',
            'dni'      => 'DNI',
            'cuño'     => 'cuño',
            'ciudad'   => 'ciudad',
            'telefono' => 'teléfono',
            'email'    => 'email',
        ];
    }
}
