<?php

namespace App\Http\Requests;

use App\Rules\Cpf;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfessorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'nome' => trim((string) $this->input('nome')),
            'profissao' => trim((string) $this->input('profissao')),
            'email' => trim((string) $this->input('email')),
            'telefone' => trim((string) $this->input('telefone')),
            'cpf' => $this->formatarCpf((string) $this->input('cpf')),
            'especialidades' => $this->normalizarEspecialidades($this->input('especialidades')),
            'horario_trabalho' => trim((string) $this->input('horario_trabalho')),
            'biografia' => trim((string) $this->input('biografia')),
            'ativo' => $this->boolean('ativo'),
            'remover_foto' => $this->boolean('remover_foto'),
        ]);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $idAtual = (int) $this->route('id');

        return [
            'nome' => ['required', 'string', 'min:3', 'max:255'],
            'profissao' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'telefone' => ['nullable', 'string', 'max:255'],
            'cpf' => ['bail', 'required', new Cpf, Rule::unique('professores', 'cpf')->ignore($idAtual)],
            'modalidade' => ['required', Rule::in(config('academia.modalidades'))],
            'especialidades' => ['required', 'array', 'min:1', 'max:6'],
            'especialidades.*' => ['string', 'max:40'],
            'biografia' => ['required', 'string', 'min:20', 'max:1000'],
            'horario_trabalho' => ['required', 'string', 'max:100'],
            'ativo' => ['boolean'],
            'foto' => ['nullable', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remover_foto' => ['boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nome.required' => 'Informe o nome do professor.',
            'nome.min' => 'O nome deve ter pelo menos 3 caracteres.',
            'nome.max' => 'O nome pode ter no máximo 255 caracteres.',
            'cpf.unique' => 'Já existe um professor cadastrado com este CPF.',
            'cpf.required' => 'Informe o CPF do professor.',
            'modalidade.required' => 'Escolha a modalidade do professor.',
            'modalidade.in' => 'Escolha uma das modalidades da lista.',
            'especialidades.required' => 'Informe pelo menos uma especialidade.',
            'especialidades.min' => 'Informe pelo menos uma especialidade.',
            'especialidades.max' => 'Informe no máximo 6 especialidades.',
            'especialidades.*.max' => 'Cada especialidade pode ter no máximo 40 caracteres.',
            'biografia.required' => 'Escreva uma biografia para o professor.',
            'biografia.min' => 'A biografia deve ter pelo menos 20 caracteres.',
            'biografia.max' => 'A biografia pode ter no máximo 1000 caracteres.',
            'horario_trabalho.required' => 'Informe o horário de trabalho.',
            'horario_trabalho.max' => 'O horário de trabalho pode ter no máximo 100 caracteres.',
            'foto.image' => 'O arquivo enviado precisa ser uma imagem.',
            'foto.mimes' => 'A foto deve estar em JPG, PNG ou WebP.',
            'foto.max' => 'A foto pode ter no máximo 2 MB.',
            'foto.uploaded' => 'Não foi possível enviar a foto. Ela pode ter mais de 2 MB.',
        ];
    }

    private function formatarCpf(string $valor): string
    {
        $digitos = preg_replace('/\D+/', '', $valor);

        if (strlen($digitos) !== 11) {
            return trim($valor);
        }

        return vsprintf('%s%s%s.%s%s%s.%s%s%s-%s%s', str_split($digitos));
    }

    /**
     * @return list<string>
     */
    private function normalizarEspecialidades(mixed $valor): array
    {
        $itens = is_array($valor) ? $valor : preg_split('/[\r\n,;]+/', (string) $valor);

        $limpos = array_map(fn (mixed $item): string => trim((string) $item), $itens ?: []);

        return array_values(array_unique(array_filter($limpos, fn (string $item): bool => $item !== '')));
    }
}
