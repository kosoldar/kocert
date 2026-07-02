<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Proceso;
use Illuminate\Http\Request;

class ProcesoController extends Controller
{
    public function index()
    {
        return response()->json(Proceso::orderBy('nombre')->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'      => 'required|string|unique:procesos,nombre',
            'descripcion' => 'nullable|string',
        ]);
        return response()->json(Proceso::create($data), 201);
    }

    public function show(Proceso $proceso)
    {
        return response()->json($proceso);
    }

    public function update(Request $request, Proceso $proceso)
    {
        $data = $request->validate([
            'nombre'      => 'sometimes|string|unique:procesos,nombre,'.$proceso->id,
            'descripcion' => 'nullable|string',
        ]);
        $proceso->update($data);
        return response()->json($proceso);
    }

    public function destroy(Proceso $proceso)
    {
        if ($proceso->certificados()->exists()) {
            return response()->json(['message' => 'Proceso tiene certificados asociados.'], 409);
        }
        $proceso->delete();
        return response()->json(null, 204);
    }
}
