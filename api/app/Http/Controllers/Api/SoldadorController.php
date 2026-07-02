<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSoldadorRequest;
use App\Http\Requests\UpdateSoldadorRequest;
use App\Http\Resources\SoldadorResource;
use App\Models\Soldador;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SoldadorController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Soldador::query();

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($q2) use ($q) {
                $q2->whereRaw('LOWER(nombre) LIKE ?',  ['%' . strtolower($q) . '%'])
                   ->orWhereRaw('LOWER(apellido) LIKE ?', ['%' . strtolower($q) . '%'])
                   ->orWhere('dni', 'ILIKE', "%{$q}%");
            });
        }

        if ($request->filled('activo')) {
            $query->where('activo', filter_var($request->activo, FILTER_VALIDATE_BOOLEAN));
        }

        $allowed  = ['apellido', 'nombre', 'dni', 'ciudad', 'email', 'created_at'];
        $sortBy   = in_array($request->sort_by, $allowed) ? $request->sort_by : 'apellido';
        $sortDir  = $request->sort_dir === 'desc' ? 'desc' : 'asc';
        $perPage  = min((int) ($request->per_page ?? 25), 100);

        $query->orderBy($sortBy, $sortDir);
        if ($sortBy !== 'apellido') $query->orderBy('apellido');

        return SoldadorResource::collection($query->paginate($perPage));
    }

    public function store(StoreSoldadorRequest $request): SoldadorResource
    {
        $soldador = Soldador::create($request->validated());
        return new SoldadorResource($soldador);
    }

    public function show(Soldador $soldador): SoldadorResource
    {
        return new SoldadorResource($soldador->load('certificados'));
    }

    public function update(UpdateSoldadorRequest $request, Soldador $soldador): SoldadorResource
    {
        $soldador->update($request->validated());
        return new SoldadorResource($soldador->fresh());
    }

    public function destroy(Soldador $soldador)
    {
        if ($soldador->certificados()->exists()) {
            return response()->json(
                ['message' => 'El soldador tiene certificados asociados y no puede eliminarse.'],
                409
            );
        }
        $soldador->delete();
        return response()->noContent();
    }

    public function uploadFoto(Request $request, Soldador $soldador): SoldadorResource
    {
        $request->validate([
            'foto' => 'required|image|mimes:png,jpg,jpeg|max:4096',
        ]);

        if ($soldador->foto_path && \Storage::exists($soldador->foto_path)) {
            \Storage::delete($soldador->foto_path);
        }

        $path = $request->file('foto')->store(
            'soldadores/' . $soldador->id . '/foto',
            'local'
        );

        $soldador->update(['foto_path' => $path]);

        return new SoldadorResource($soldador->fresh());
    }
}
