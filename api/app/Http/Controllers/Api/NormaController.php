<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Norma;
use Illuminate\Http\Request;

class NormaController extends Controller
{
    public function index()
    {
        return response()->json(Norma::orderBy('nombre')->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'      => 'required|string|unique:normas,nombre',
            'descripcion' => 'nullable|string',
        ]);
        return response()->json(Norma::create($data), 201);
    }

    public function show(Norma $norma)
    {
        return response()->json($norma);
    }

    public function update(Request $request, Norma $norma)
    {
        $data = $request->validate([
            'nombre'      => 'sometimes|string|unique:normas,nombre,'.$norma->id,
            'descripcion' => 'nullable|string',
        ]);
        $norma->update($data);
        return response()->json($norma);
    }

    public function destroy(Norma $norma)
    {
        if ($norma->certificados()->exists()) {
            return response()->json(['message' => 'Norma tiene certificados asociados.'], 409);
        }
        $norma->delete();
        return response()->json(null, 204);
    }
}
