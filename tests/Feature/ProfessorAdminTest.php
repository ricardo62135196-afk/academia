<?php

namespace Tests\Feature;

use App\Models\Professor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ProfessorAdminTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function lista_professores_do_banco_com_acoes_de_edicao_e_exclusao(): void
    {
        $this->get(route('admin.professores.index'))
            ->assertOk()
            ->assertSee('Carlos Silva')
            ->assertSee('Novo professor')
            ->assertSee(route('admin.professores.edit', Professor::query()->where('nome', 'Carlos Silva')->value('id')), false)
            ->assertSee('Excluir');
    }

    #[Test]
    public function cadastro_salva_dados_no_banco_e_foto_fora_do_banco(): void
    {
        Storage::fake('public');

        $this->post(route('admin.professores.store'), $this->dadosValidos([
            'foto' => $this->fotoPng(),
        ]))
            ->assertRedirect(route('admin.professores.index'))
            ->assertSessionHas('sucesso');

        $professor = Professor::query()->where('cpf', '529.982.247-25')->firstOrFail();

        $this->assertSame('Personal trainer', $professor->profissao);
        $this->assertStringStartsWith('professores/', $professor->foto);
        Storage::disk('public')->assertExists($professor->foto);
    }

    #[Test]
    public function cadastro_sem_foto_usa_a_imagem_padrao(): void
    {
        $this->post(route('admin.professores.store'), $this->dadosValidos())
            ->assertSessionHasNoErrors();

        $this->assertSame(
            config('academia.foto_padrao'),
            Professor::query()->where('cpf', '529.982.247-25')->value('foto')
        );
    }

    #[Test]
    public function exige_dados_validos_e_nao_cadastra_em_caso_de_erro(): void
    {
        $quantidadeInicial = Professor::query()->count();

        $this->post(route('admin.professores.store'), [])
            ->assertSessionHasErrors(['nome', 'cpf', 'modalidade', 'especialidades', 'biografia', 'horario_trabalho']);

        $this->assertDatabaseCount('professores', $quantidadeInicial);
    }

    #[Test]
    public function rejeita_cpf_duplicado(): void
    {
        $cpfExistente = Professor::query()->value('cpf');

        $this->post(route('admin.professores.store'), $this->dadosValidos(['cpf' => $cpfExistente]))
            ->assertSessionHasErrors(['cpf' => 'Já existe um professor cadastrado com este CPF.']);
    }

    #[Test]
    public function rejeita_arquivo_que_nao_e_imagem(): void
    {
        Storage::fake('public');
        $quantidadeInicial = Professor::query()->count();

        $this->post(route('admin.professores.store'), $this->dadosValidos([
            'foto' => UploadedFile::fake()->create('documento.pdf', 10, 'application/pdf'),
        ]))->assertSessionHasErrors('foto');

        $this->assertDatabaseCount('professores', $quantidadeInicial);
        $this->assertSame([], Storage::disk('public')->allFiles('professores'));
    }

    #[Test]
    public function atualiza_dados_e_mantem_a_foto_atual(): void
    {
        Storage::fake('public');
        $professor = Professor::query()->where('nome', 'Carlos Silva')->firstOrFail();

        $this->put(route('admin.professores.update', $professor), $this->dadosValidos([
            'cpf' => $professor->cpf,
            'nome' => 'Carlos Silva Junior',
            'profissao' => 'Educador físico',
            'modalidade' => 'Funcional',
        ]))
            ->assertRedirect(route('admin.professores.index'))
            ->assertSessionHas('sucesso');

        $professor->refresh();

        $this->assertSame('Carlos Silva Junior', $professor->nome);
        $this->assertSame('Educador físico', $professor->profissao);
        $this->assertSame('Funcional', $professor->modalidade);
        $this->assertSame('img/professores/professor2.avif', $professor->foto);
    }

    #[Test]
    public function atualiza_foto_e_apaga_a_foto_enviada_anterior(): void
    {
        Storage::fake('public');
        $professor = Professor::query()->where('nome', 'Carlos Silva')->firstOrFail();
        $caminhoAnterior = $this->fotoPng('anterior.png')->storePublicly('professores', 'public');
        $professor->update(['foto' => $caminhoAnterior]);

        $this->put(route('admin.professores.update', $professor), $this->dadosValidos([
            'cpf' => $professor->cpf,
            'foto' => $this->fotoPng(),
        ]))->assertSessionHasNoErrors();

        $professor->refresh();

        Storage::disk('public')->assertMissing($caminhoAnterior);
        Storage::disk('public')->assertExists($professor->foto);
    }

    #[Test]
    public function exclui_professor_e_foto_enviada_sem_apagar_fotos_legadas(): void
    {
        Storage::fake('public');
        $professor = Professor::query()->where('nome', 'Carlos Silva')->firstOrFail();
        $caminhoFoto = $this->fotoPng('professor.png')->storePublicly('professores', 'public');
        $professor->update(['foto' => $caminhoFoto]);

        $this->delete(route('admin.professores.destroy', $professor))
            ->assertRedirect(route('admin.professores.index'))
            ->assertSessionHas('sucesso');

        $this->assertDatabaseMissing('professores', ['id' => $professor->id]);
        Storage::disk('public')->assertMissing($caminhoFoto);
        $this->assertFileExists(public_path('img/professores/professor2.avif'));
    }

    #[Test]
    public function api_publica_le_do_banco_sem_expor_o_cpf(): void
    {
        Storage::fake('public');
        $professor = Professor::query()->where('nome', 'Carlos Silva')->firstOrFail();
        $professor->update(['foto' => 'professores/carlos.png']);

        $resposta = $this->getJson('/api/professores')->assertOk();

        $resposta->assertJsonFragment([
            'id' => $professor->id,
            'nome' => 'Carlos Silva',
            'profissao' => null,
            'foto' => url('storage/professores/carlos.png'),
        ]);
        $this->assertStringNotContainsString($professor->cpf, $resposta->getContent());
    }

    #[Test]
    public function cadastro_antigo_redireciona_para_o_gerenciamento(): void
    {
        $this->get(route('professores.create'))
            ->assertRedirect(route('admin.professores.index'));
    }

    #[Test]
    public function perfis_publicos_continuam_disponiveis(): void
    {
        $this->get('/professores/1')
            ->assertOk()
            ->assertSee('perfil-professor');
    }

    /**
     * @param  array<string, mixed>  $extra
     * @return array<string, mixed>
     */
    private function dadosValidos(array $extra = []): array
    {
        return array_merge([
            'nome' => 'Lucas Pereira',
            'profissao' => 'Personal trainer',
            'email' => 'lucas@example.com',
            'telefone' => '47999999999',
            'cpf' => '529.982.247-25',
            'modalidade' => 'Pilates',
            'especialidades' => "Yoga\nRespiração",
            'biografia' => 'Instrutor com anos de experiência em aulas em grupo e atendimento individual.',
            'horario_trabalho' => 'Segunda a sexta, 08:00 às 16:00',
            'ativo' => '1',
        ], $extra);
    }

    private function fotoPng(string $nome = 'foto.png'): UploadedFile
    {
        $caminho = tempnam(sys_get_temp_dir(), 'professor-foto-');
        file_put_contents($caminho, base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg=='
        ));

        return new UploadedFile($caminho, $nome, 'image/png', null, true);
    }
}
