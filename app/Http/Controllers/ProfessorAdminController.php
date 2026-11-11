<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfessorRequest;
use App\Models\Professor;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class ProfessorAdminController extends Controller
{
    public function index(): View
    {
        $professores = Professor::query()
            ->orderBy('nome')
            ->get();

        return view('admin.professores.index', ['professores' => $professores]);
    }

    public function create(): View
    {
        return view('admin.professores.form', ['professor' => null]);
    }

    public function store(ProfessorRequest $request): RedirectResponse
    {
        $dados = $this->dadosDoProfessor($request);
        $dados['foto'] = $request->hasFile('foto')
            ? $this->salvarFoto($request->file('foto'))
            : config('academia.foto_padrao');
        $dados['ativo'] = $request->boolean('ativo');

        $professor = Professor::query()->create($dados);

        return redirect()
            ->route('admin.professores.index')
            ->with('sucesso', "Professor {$professor->nome} cadastrado com sucesso.");
    }

    public function edit(int $id): View
    {
        return view('admin.professores.form', [
            'professor' => Professor::query()->findOrFail($id),
        ]);
    }

    public function update(ProfessorRequest $request, int $id): RedirectResponse
    {
        $professor = Professor::query()->findOrFail($id);
        $fotoAnterior = $professor->foto;
        $dados = $this->dadosDoProfessor($request);

        if ($request->hasFile('foto')) {
            $dados['foto'] = $this->salvarFoto($request->file('foto'));
        } elseif ($request->boolean('remover_foto')) {
            $dados['foto'] = config('academia.foto_padrao');
        }

        $dados['ativo'] = $request->boolean('ativo');
        $professor->update($dados);

        if (isset($dados['foto']) && $dados['foto'] !== $fotoAnterior) {
            $this->apagarFoto($fotoAnterior);
        }

        return redirect()
            ->route('admin.professores.index')
            ->with('sucesso', "Professor {$professor->nome} atualizado com sucesso.");
    }

    public function destroy(int $id): RedirectResponse
    {
        $professor = Professor::query()->findOrFail($id);
        $nome = $professor->nome;
        $foto = $professor->foto;
        $professor->delete();
        $this->apagarFoto($foto);

        return redirect()
            ->route('admin.professores.index')
            ->with('sucesso', "Professor {$nome} excluído.");
    }

    /**
     * @return array<string, mixed>
     */
    private function dadosDoProfessor(ProfessorRequest $request): array
    {
        $validado = $request->validated();

        return [
            'nome' => $validado['nome'],
            'profissao' => $validado['profissao'] ?? null,
            'email' => $validado['email'] ?? null,
            'telefone' => $validado['telefone'] ?? null,
            'cpf' => $validado['cpf'],
            'modalidade' => $validado['modalidade'],
            'especialidades' => $validado['especialidades'],
            'biografia' => $validado['biografia'],
            'horario_trabalho' => $validado['horario_trabalho'],
        ];
    }

    private function salvarFoto(UploadedFile $arquivo): string
    {
        $caminho = $arquivo->storePublicly('professores', 'public');

        if ($caminho === false) {
            throw new RuntimeException('Não foi possível salvar a foto do professor.');
        }

        return $caminho;
    }

    private function apagarFoto(?string $caminho): void
    {
        if ($caminho === null || ! Str::startsWith($caminho, 'professores/')) {
            return;
        }

        $disco = Storage::disk('public');

        if ($disco->exists($caminho) && ! $disco->delete($caminho)) {
            throw new RuntimeException('Não foi possível remover a foto anterior do professor.');
        }
    }
}
