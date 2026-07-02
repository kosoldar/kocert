<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Material;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    public function index()
    {
        return response()->json(Material::orderBy('nombre')->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'      => 'required|string|unique:materiales,nombre',
            'descripcion' => 'nullable|string',
        ]);
        return response()->json(Material::create($data), 201);
    }

    public function show(Material $material)
    {
        return response()->json($material);
    }

    public function update(Request $request, Material $material)
    {
        $data = $request->validate([
            'nombre'      => 'sometimes|string|unique:materiales,nombre,'.$material->id,
            'descripcion' => 'nullable|string',
        ]);
        $material->update($data);
        return response()->json($material);
    }

    public function destroy(Material $material)
    {
        if ($material->certificados()->exists()) {
            return response()->json(['message' => 'Material tiene certificados asociados.'], 409);
        }
        $material->delete();
        return response()->json(null, 204);
    }
}
