<?php

namespace App\Http\Requests\Concerns;

use App\Models\Norma;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Validator;

trait ValidaCombinacionesNorma
{
    /**
     * Registra la validación de combinaciones normativas en el validator.
     * Llamar desde withValidator() del FormRequest.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(fn($v) => $this->validarCombinaciones($v));
    }

    private function validarCombinaciones(Validator $v): void
    {
        // Resolve norma (la solicitada o la del certificado existente en edición)
        $normaId = $this->norma_id ?? $this->route('certificado')?->norma_id;
        $norma   = Norma::find($normaId);
        if (!$norma) return;

        $effective = $norma->parent ?? $norma;
        $cert      = $this->route('certificado');

        $proceso  = $this->proceso  ?? $cert?->proceso;
        $posicion = $this->posicion ?? $cert?->posicion;
        $vars     = array_merge(
            is_array($cert?->variables) ? $cert->variables : [],
            is_array($this->variables)  ? $this->variables  : [],
        );

        // ── 1. Proceso válido para la norma ──────────────────────────────────
        if ($proceso && DB::table('norma_proceso')->where('norma_id', $norma->id)->exists()) {
            $procesoValido = DB::table('norma_proceso')
                ->join('procesos', 'procesos.id', '=', 'norma_proceso.proceso_id')
                ->where('norma_proceso.norma_id', $norma->id)
                ->where('procesos.nombre', $proceso)
                ->exists();

            if (!$procesoValido) {
                $v->errors()->add(
                    'proceso',
                    "El proceso '{$proceso}' no está permitido por {$norma->nombre}."
                );
            }
        }

        // ── 2. Metal base + grupo consumible compatibles ──────────────────────
        $metalCode  = $vars['p_number']        ?? $vars['grupo_base_metal'] ?? null;
        $consumCode = $vars['f_number']         ?? $vars['grupo_consumible'] ?? null;

        if ($metalCode && $consumCode) {
            $hasPivot = DB::table('grupo_base_metal_consumible as gbc')
                ->join('grupos_base_metal as gbm', 'gbm.id', '=', 'gbc.grupo_base_metal_id')
                ->where('gbm.norma_id', $effective->id)
                ->exists();

            if ($hasPivot) {
                $compatible = DB::table('grupo_base_metal_consumible as gbc')
                    ->join('grupos_base_metal as gbm', 'gbm.id', '=', 'gbc.grupo_base_metal_id')
                    ->join('grupos_consumible as gc',  'gc.id',  '=', 'gbc.grupo_consumible_id')
                    ->where('gbm.norma_id', $effective->id)
                    ->where('gbm.codigo',   $metalCode)
                    ->where('gc.codigo',    $consumCode)
                    ->exists();

                if (!$compatible) {
                    $campo = isset($vars['f_number']) ? 'variables.f_number' : 'variables.grupo_consumible';
                    $v->errors()->add(
                        $campo,
                        "El consumible '{$consumCode}' no es compatible con '{$metalCode}' según {$norma->nombre}."
                    );
                }
            }
        }

        // ── 4. GMAW-S: no cambiar entre modos incompatibles al editar (QW-410.26) ─
        $transferType = $vars['transfer_type'] ?? null;
        $isGmaw       = $proceso && in_array(strtoupper($proceso), ['GMAW', 'GMAW-S']);

        if ($isGmaw && $transferType && $cert) {
            $originalType = ($cert->variables ?? [])['transfer_type'] ?? null;
            if ($originalType && $originalType !== $transferType) {
                $shortCircuit = ['short_circuit'];
                $highEnergy   = ['spray', 'pulse', 'globular'];
                $conflict = (in_array($originalType, $shortCircuit) && in_array($transferType, $highEnergy))
                         || (in_array($originalType, $highEnergy)   && in_array($transferType, $shortCircuit));
                if ($conflict) {
                    $v->errors()->add(
                        'variables.transfer_type',
                        "No se puede cambiar entre short-circuit y spray/pulse/globular (QW-410.26). Son calificaciones independientes."
                    );
                }
            }
        }

        // ── 3. Consumible + posición compatibles (solo si hay restricción) ───
        if ($consumCode && $posicion) {
            $hasRestriction = DB::table('grupo_consumible_posicion as gcp')
                ->join('grupos_consumible as gc', 'gc.id', '=', 'gcp.grupo_consumible_id')
                ->where('gc.norma_id', $effective->id)
                ->where('gc.codigo',   $consumCode)
                ->exists();

            if ($hasRestriction) {
                $posicionPermitida = DB::table('grupo_consumible_posicion as gcp')
                    ->join('grupos_consumible as gc', 'gc.id', '=', 'gcp.grupo_consumible_id')
                    ->join('posiciones as p',          'p.id',  '=', 'gcp.posicion_id')
                    ->where('gc.norma_id', $effective->id)
                    ->where('gc.codigo',   $consumCode)
                    ->where('p.codigo',    $posicion)
                    ->exists();

                if (!$posicionPermitida) {
                    $v->errors()->add(
                        'posicion',
                        "La posición '{$posicion}' no está permitida para '{$consumCode}' según {$norma->nombre}."
                    );
                }
            }
        }
    }
}
