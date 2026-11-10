<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfessorRequest;
use App\Support\ProfessorRepository;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class ProfessorAdminController extends Controller
{
    public function __construct(private readonly ProfessorRepository $professores) {}

    public function index(): View
    {
        $professores = collect($this->professores->all())
            ->sortBy(fn (array $professor): string => Str::lower(Str::ascii($professor['nome'])))
            ->values()
            ->all();

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
            ? $this->salvarFoto($request->file('foto'), $dados['nome'])
            : config('academia.foto_padrao');
        $dados['ativo'] = $request->boolean('ativo');

        $professor = $this->professores->create($dados);

        return redirect()
            ->route('admin.professores.index')
            ->with('sucesso', "Professor {$professor['nome']} cadastrado com sucesso.");
    }

    public function edit(int $id): View
    {
        return view('admin.professores.form', ['professor' => $this->professorOuErro404($id)]);
    }

    public function update(ProfessorRequest $request, int $id): RedirectResponse
    {
        $atual = $this->professorOuErro404($id);

        $dados = $this->dadosDoProfessor($request);
        $dados['foto'] = $atual['foto'];

        if ($request->hasFile('foto')) {
            $this->apagarFoto($atual['foto']);
            $dados['foto'] = $this->salvarFoto($request->file('foto'), $dados['nome']);
        } elseif ($request->boolean('remover_foto')) {
            $this->apagarFoto($atual['foto']);
            $dados['foto'] = config('academia.foto_padrao');
        }

        $dados['ativo'] = $request->boolean('ativo');

        $this->professores->update($id, $dados);

        return redirect()
            ->route('admin.professores.index')
            ->with('sucesso', "Professor {$dados['nome']} atualizado com sucesso.");
    }

    public function destroy(int $id): RedirectResponse
    {
        $removido = $this->professores->delete($id);

        if ($removido === null) {
            abort(404);
        }

        $this->apagarFoto($removido['foto']);

        return redirect()
            ->route('admin.professores.index')
            ->with('sucesso', "Professor {$removido['nome']} excluído.");
    }

    /**
     * @return array<string, mixed>
     */
    private function professorOuErro404(int $id): array
    {
        return $this->professores->find($id) ?? abort(404);
    }

    /**
     * @return array<string, mixed>
     */
    private function dadosDoProfessor(ProfessorRequest $request): array
    {
        $validado = $request->validated();

        return [
            'nome' => $validado['nome'],
            'cpf' => $validado['cpf'],
            'modalidade' => $validado['modalidade'],
            'especialidades' => $validado['especialidades'],
            'biografia' => $validado['biografia'],
            'horario_trabalho' => $validado['horario_trabalho'],
        ];
    }

    private function salvarFoto(UploadedFile $arquivo, string $nome): string
    {
        $nomeDoArquivo = Str::slug($nome).'-'.Str::lower(Str::random(6)).'.'.$arquivo->guessExtension();

        $arquivo->move(config('academia.fotos_diretorio'), $nomeDoArquivo);

        return config('academia.fotos_url_base').'/'.$nomeDoArquivo;
    }

    private function apagarFoto(string $caminho): void
    {
        if ($caminho === config('academia.foto_padrao')) {
            return;
        }

        $arquivo = config('academia.fotos_diretorio').DIRECTORY_SEPARATOR.basename($caminho);

        if (is_file($arquivo)) {
            unlink($arquivo);
        }
    }
}
