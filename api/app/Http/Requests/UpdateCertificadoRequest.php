<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\ValidaCombinacionesNorma;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCertificadoRequest extends FormRequest
{
    use ValidaCombinacionesNorma;

    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'soldador_id'        => 'sometimes|exists:soldadores,id',
            'empresa_id'         => 'sometimes|exists:empresas,id',
            'norma_id'           => 'sometimes|exists:normas,id',
            'tipo'               => 'sometimes|in:inicial,renovacion,ampliacion',
            'resultado'          => 'sometimes|in:aprobado,rechazado',
            'estado'             => 'sometimes|in:borrador,vigente,vencido,suspendido',
            'fecha_calificacion' => 'sometimes|date',
            'fecha_vencimiento'  => 'nullable|date',
            'eps_numero'         => 'sometimes|string|max:50',
            'pqr_numero'         => 'nullable|string|max:50',
            'proceso'            => 'sometimes|string|max:20',
            'posicion'           => 'sometimes|string|max:10',
            'progresion'         => 'nullable|in:ascendente,descendente',
            'tipo_cupon'         => 'sometimes|in:caño,chapa',
            'variables'          => 'nullable|array',
            'joint_design_id'             => 'nullable|exists:joint_designs,id',
            'joint_detail'                => 'nullable|string|max:200',
            'inspector_id'                => 'nullable|exists:inspectors,id',
            'observaciones'               => 'nullable|string',
            'passes'                      => 'nullable|array',
            'passes.*.etiqueta'           => 'required_with:passes.*|string|max:100',
            'passes.*.proceso_id'         => 'nullable|exists:procesos,id',
            'passes.*.clasificacion_aporte' => 'nullable|string|max:50',
            'passes.*.diametro_aporte_mm' => 'nullable|numeric|min:0',
            'passes.*.polaridad'          => 'nullable|string|max:20',
            'passes.*.amperaje_min'       => 'nullable|integer|min:0',
            'passes.*.amperaje_max'       => 'nullable|integer|min:0',
            'passes.*.voltaje_min'        => 'nullable|integer|min:0',
            'passes.*.voltaje_max'        => 'nullable|integer|min:0',
            'passes.*.velocidad_avance_min' => 'nullable|integer|min:0',
            'passes.*.velocidad_avance_max' => 'nullable|integer|min:0',
            'passes.*.progresion'         => 'nullable|in:ascendente,descendente',
        ];
    }
}
