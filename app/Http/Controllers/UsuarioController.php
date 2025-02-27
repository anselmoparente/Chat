<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;

class UsuarioController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'nome' => 'required|string'
        ]);

        $users = Usuario::where('nome', '!=', $request->nome)->get();

        return response()->json($users);
    }

    public function store(Request $request)
    {

        $request->validate([
            'nome' => 'required|string|max:255',
            'latitude' => 'required|string',
            'longitude' => 'required|string',
        ]);

        $user = Usuario::where('nome', $request->nome)->first();

        if ($user) {
            $user->update([
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
            ]);

            return response()->json([
                'message' => 'Localização do usuário atualizada!',
                'user' => $user,
            ], 200);
        }

        $user = Usuario::create([
            'nome' => $request->nome,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        return response()->json([
            'message' => 'Usuário criado com sucesso!',
            'user' => $user,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'latitude' => 'required|string',
            'longitude' => 'required|string',
        ]);

        $user = Usuario::find($id);

        if (!$user) {
            return response()->json([
                'message' => 'Usuário não encontrado!',
            ], 404);
        }

        $user->update([
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        return response()->json([
            'message' => 'Localização do usuário atualizada!',
            'user' => $user,
        ], 200);
    }
}
