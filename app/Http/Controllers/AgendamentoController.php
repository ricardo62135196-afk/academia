<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AgendamentoController extends Controller
{
    public function index()
    {
        return view('agendamento');
    }

    public function store(Request $request)
    {
        $dados = $request->validate([
            'nome'       => 'required|string|max:100',
            'modalidade' => 'required|in:Musculação,Pilates,Funcional,Spinning',
            'professor'  => 'nullable|string|max:100',
            'data'       => 'required|date|after_or_equal:today',
            'horario'    => 'required|date_format:H:i',
        ]);

        // Aqui você pode salvar no banco, se quiser.

        return back()->with('sucesso', "Aula de {$dados['modalidade']} agendada para {$dados['data']} às {$dados['horario']}, {$dados['nome']}!");
    }
}