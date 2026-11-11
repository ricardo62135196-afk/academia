<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class ProfessorCadastroController extends Controller
{
    public function create(): View
    {
        return view('professor-cadastro');
    }

    public function store(Request $request): RedirectResponse
    {
        $dados = $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'profissao' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'telefone' => ['required', 'string', 'max:255'],
        ]);

        DB::table('professores')->insert([
            ...$dados,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()
            ->route('professores.create')
            ->with('sucesso', 'Professor cadastrado com sucesso.');
    }
}
