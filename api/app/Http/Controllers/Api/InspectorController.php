<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Inspector;
use Illuminate\Http\Request;

class InspectorController extends Controller
{
    public function index()
    {
        return response()->json(
            Inspector::orderBy('nombre')->get()
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'        => 'required|string|max:100',
            'certificacion' => 'required|string|max:200',
            'email'         => 'nullable|email|unique:inspectors,email|max:100',
            'telefono'      => 'nullable|string|max:30',
            'activo'        => 'boolean',
        ]);

        $inspector = Inspector::create($data);

        return response()->json($inspector, 201);
    }

    public function show(Inspector $inspector)
    {
        return response()->json($inspector);
    }

    public function update(Request $request, Inspector $inspector)
    {
        $data = $request->validate([
            'nombre'        => 'sometimes|string|max:100',
            'certificacion' => 'sometimes|string|max:200',
            'email'         => 'nullable|email|max:100|unique:inspectors,email,' . $inspector->id,
            'telefono'      => 'nullable|string|max:30',
            'activo'        => 'boolean',
        ]);

        $inspector->update($data);

        return response()->json($inspector->fresh());
    }

    public function destroy(Inspector $inspector)
    {
        $inspector->update(['activo' => false]);
        return response()->json(null, 204);
    }

    public function uploadFirma(Request $request, Inspector $inspector)
    {
        $request->validate([
            'firma' => 'required|image|mimes:png,jpg,jpeg|max:2048',
        ]);

        if ($inspector->firma_path && \Storage::exists($inspector->firma_path)) {
            \Storage::delete($inspector->firma_path);
        }

        $path = $request->file('firma')->store(
            'inspectors/' . $inspector->id . '/firma',
            'local'
        );

        $inspector->update(['firma_path' => $path]);

        return response()->json(['firma_path' => $path]);
    }
}
