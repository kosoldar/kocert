<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\ValidaCombinacionesNorma;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreCertificadoRequest extends FormRequest
{
    use ValidaCombinacionesNorma;

    public function authorize(): bool { return true; }

    // Override trait's withValidator to skip combination checks on drafts
    public function withValidator(Validator $validator): void
    {
        if (!$this->boolean('borrador')) {
            $validator->after(fn($v) => $this->validarCombinaciones($v));
        }
    }

    public function rules(): array
    {
        $anio = $this->input('anio', date('Y'));

        if ($this->boolean('borrador')) {
            return [
                'numero'             => ['required', 'integer', 'min:1', "unique:certificados,numero,NULL,id,anio,{$anio}"],
                'anio'               => 'required|integer|min:2000',
                'revision'           => 'nullable|integer|min:0',
                'soldador_id'        => 'nullable|exists:soldadores,id',
                'empresa_id'         => 'nullable|exists:empresas,id',
                'norma_id'           => 'nullable|exists:normas,id',
                'inspector_id'       => 'nullable|exists:inspectors,id',
                'tipo'               => 'nullable|in:inicial,renovacion,ampliacion',
                'resultado'          => 'nullable|in:aprobado,rechazado',
                'fecha_calificacion' => 'nullable|date',
                'fecha_vencimiento'  => 'nullable|date',
                'eps_numero'         => 'nullable|string|max:50',
                'pqr_numero'         => 'nullable|string|max:50',
                'proceso'            => 'nullable|string|max:20',
                'posicion'           => 'nullable|string|max:10',
                'progresion'         => 'nullable|in:ascendente,descendente',
                'tipo_cupon'         => 'nullable|in:caño,chapa',
                'variables'          => 'nullable|array',
                'passes'             => 'nullable|array',
                'passes.*.etiqueta'  => 'nullable|string|max:100',
                'observaciones'      => 'nullable|string',
            ];
        }

        return [
            'numero'             => ['required', 'integer', 'min:1', "unique:certificados,numero,NULL,id,anio,{$anio}"],
            'anio'               => 'required|integer|min:2000',
            'revision'           => 'integer|min:0',
            'soldador_id'        => 'required|exists:soldadores,id',
            'empresa_id'         => 'required|exists:empresas,id',
            'norma_id'           => 'required|exists:normas,id',
            'inspector_id'       => 'required|exists:inspectors,id',
            'tipo'               => 'required|in:inicial,renovacion,ampliacion',
            'resultado'          => 'required|in:aprobado,rechazado',
            'fecha_calificacion' => 'required|date',
            'fecha_vencimiento'  => 'nullable|date|after:fecha_calificacion',
            'eps_numero'         => 'required|string|max:50',
            'pqr_numero'         => 'nullable|string|max:50',
            'proceso'            => 'required|string|max:20',
            'posicion'           => 'required|string|max:10',
            'progresion'         => 'nullable|in:ascendente,descendente',
            'tipo_cupon'         => 'required|in:caño,chapa',
            'variables'          => 'nullable|array',
            'joint_design_id'             => 'nullable|exists:joint_designs,id',
            'joint_detail'                => 'nullable|string|max:200',
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
