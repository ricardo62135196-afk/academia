<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AlunosController extends Controller
{
    public function index(): View
    {
        return view('alunos');
    }

    public function store(Request $request): RedirectResponse
    {
        $dados = $request->validate([
            'nome' => 'required|string|max:100',
            'email' => 'required|email|max:30',
            'telefone' => 'required|string|max:20',
            'password' => 'required|string|min:8|max:255',
        ]);

        DB::insert(
            'INSERT INTO alunos (nome, email, telefone, password, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?)',
            [
                $dados['nome'],
                $dados['email'],
                $dados['telefone'],
                Hash::make($dados['password']),
                now(),
                now(),
            ]
        );

        return redirect()
            ->route('alunos.index')
            ->with('sucesso', 'Aluno cadastrado com sucesso.');
    }
}
