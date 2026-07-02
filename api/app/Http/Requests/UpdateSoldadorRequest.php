<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSoldadorRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $id = $this->route('soldador')->id;

        return [
            'nombre'           => 'sometimes|required|string|max:100',
            'apellido'         => 'sometimes|required|string|max:100',
            'dni'              => "sometimes|required|string|max:20|unique:soldadores,dni,{$id}",
            'fecha_nacimiento' => 'nullable|date',
            'nacionalidad'     => 'nullable|string|max:100',
            'cuño'             => 'nullable|string|max:20',
            'ciudad'   => 'nullable|string|max:100',
            'telefono' => 'nullable|string|max:30',
            'email'    => 'nullable|email|max:100',
            'activo'   => 'sometimes|boolean',
        ];
    }
}
