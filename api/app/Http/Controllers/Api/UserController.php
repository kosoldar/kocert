<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($q2) use ($q) {
                $q2->whereRaw('LOWER(nombre) LIKE ?',   ['%' . strtolower($q) . '%'])
                   ->orWhereRaw('LOWER(apellido) LIKE ?', ['%' . strtolower($q) . '%'])
                   ->orWhereRaw('LOWER(email) LIKE ?',    ['%' . strtolower($q) . '%']);
            });
        }

        if ($request->filled('activo')) {
            $query->where('activo', filter_var($request->activo, FILTER_VALIDATE_BOOLEAN));
        }

        if ($request->filled('rol')) {
            $query->where('rol', $request->rol);
        }

        $allowed = ['apellido', 'nombre', 'email', 'rol', 'created_at'];
        $sortBy  = in_array($request->sort_by, $allowed) ? $request->sort_by : 'apellido';
        $sortDir = $request->sort_dir === 'desc' ? 'desc' : 'asc';
        $perPage = min((int) ($request->per_page ?? 25), 100);

        $query->orderBy($sortBy, $sortDir);
        if ($sortBy !== 'apellido') $query->orderBy('apellido');

        return response()->json($query->paginate($perPage));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'   => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'rol'      => 'required|in:admin,tecnico',
            'activo'   => 'sometimes|boolean',
        ]);

        $data['activo'] = $data['activo'] ?? true;

        return response()->json(User::create($data), 201);
    }

    public function show(User $user)
    {
        return response()->json($user);
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'nombre'   => 'sometimes|string|max:100',
            'apellido' => 'sometimes|string|max:100',
            'email'    => 'sometimes|email|unique:users,email,' . $user->id,
            'password' => 'sometimes|nullable|string|min:8',
            'rol'      => 'sometimes|in:admin,tecnico',
            'activo'   => 'sometimes|boolean',
        ]);

        if (array_key_exists('password', $data) && empty($data['password'])) {
            unset($data['password']);
        }

        $user->update($data);
        return response()->json($user->fresh()->makeHidden(['password', 'remember_token']));
    }

    public function destroy(User $user)
    {
        if ($user->certificados()->exists()) {
            return response()->json(['message' => 'El usuario tiene certificados asociados.'], 409);
        }
        $user->delete();
        return response()->json(null, 204);
    }
}
