<?php

namespace App\Http\Controllers;

use App\Models\Mensagem;
use App\Models\Usuario;
use Illuminate\Http\Request;

class MensagemController extends Controller
{
    public function index($id)
    {
        $user = Usuario::find($id);

        if (!$user) {
            return response()->json(['message' => 'Usuário não encontrado'], 404);
        }

        $mensagens = Mensagem::where(function ($query) use ($id) {
            $query->where('usuario_envio_id', $id)
                ->orWhere('usuario_recebimento_id', $id);
        })->orderBy('created_at', 'asc')->get();

        return response()->json($mensagens);
    }

    public function store(Request $request)
    {
        $request->validate([
            'texto' => 'required|string',
            'usuario_envio_id' => 'required|exists:usuarios,id',
            'usuario_recebimento_id' => 'required|exists:usuarios,id',
            'near' => 'required|boolean',
        ]);

        $mensagem = Mensagem::create([
            'texto' => $request->texto,
            'usuario_envio_id' => $request->usuario_envio_id,
            'usuario_recebimento_id' => $request->usuario_recebimento_id,
            'near' => $request->near,
        ]);

        return response()->json($mensagem, 201);
    }
}
