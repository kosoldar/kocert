<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use Illuminate\Http\Request;

class EmpresaController extends Controller
{
    public function index(Request $request)
    {
        $query = Empresa::query();

        if ($request->filled('search')) {
            $q = $request->search;
            $query->whereRaw('LOWER(nombre) LIKE ?', ['%' . strtolower($q) . '%'])
                  ->orWhereRaw('LOWER(cuit) LIKE ?', ['%' . strtolower($q) . '%']);
        }

        if ($request->filled('activo')) {
            $query->where('activo', filter_var($request->activo, FILTER_VALIDATE_BOOLEAN));
        }

        $allowed = ['nombre', 'cuit', 'created_at'];
        $sortBy  = in_array($request->sort_by, $allowed) ? $request->sort_by : 'nombre';
        $sortDir = $request->sort_dir === 'desc' ? 'desc' : 'asc';
        $perPage = min((int) ($request->per_page ?? 25), 100);

        $query->orderBy($sortBy, $sortDir);

        return response()->json($query->paginate($perPage));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'   => 'required|string|max:200|unique:empresas,nombre',
            'cuit'     => 'nullable|string|max:20',
            'contacto' => 'nullable|string|max:100',
            'activo'   => 'sometimes|boolean',
        ]);

        return response()->json(Empresa::create($data), 201);
    }

    public function show(Empresa $empresa)
    {
        return response()->json($empresa);
    }

    public function update(Request $request, Empresa $empresa)
    {
        $data = $request->validate([
            'nombre'   => 'sometimes|string|max:200|unique:empresas,nombre,' . $empresa->id,
            'cuit'     => 'nullable|string|max:20',
            'contacto' => 'nullable|string|max:100',
            'activo'   => 'sometimes|boolean',
        ]);

        $empresa->update($data);
        return response()->json($empresa->fresh());
    }

    public function destroy(Empresa $empresa)
    {
        if ($empresa->certificados()->exists()) {
            return response()->json(['message' => 'Empresa tiene certificados asociados.'], 409);
        }
        $empresa->delete();
        return response()->json(null, 204);
    }
}
