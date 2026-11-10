<?php

namespace Tests\Feature;

use App\Rules\Cpf;
use Illuminate\Http\UploadedFile;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ProfessorAdminTest extends TestCase
{
    private string $pastaBase;

    private string $arquivoJson;

    private string $pastaFotos;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pastaBase = sys_get_temp_dir().'/academia-teste-'.uniqid();
        $this->pastaFotos = $this->pastaBase.'/fotos';
        $this->arquivoJson = $this->pastaBase.'/professores.json';

        mkdir($this->pastaFotos, 0777, true);
        copy(base_path('database/data/professores.json'), $this->arquivoJson);

        config([
            'academia.professores_arquivo' => $this->arquivoJson,
            'academia.fotos_diretorio' => $this->pastaFotos,
        ]);
    }

    protected function tearDown(): void
    {
        foreach (glob($this->pastaFotos.'/*') ?: [] as $arquivo) {
            unlink($arquivo);
        }
        @rmdir($this->pastaFotos);
        @unlink($this->arquivoJson);
        @rmdir($this->pastaBase);

        parent::tearDown();
    }

    #[Test]
    public function lista_os_professores_com_acoes_de_editar_e_excluir(): void
    {
        $this->get('/admin/professores')
            ->assertOk()
            ->assertSee('Carlos Silva')
            ->assertSee('Novo professor')
            ->assertSee(route('admin.professores.edit', 1), false)
            ->assertSee('Excluir');
    }

    #[Test]
    public function cadastra_professor_com_foto(): void
    {
        $resposta = $this->post(route('admin.professores.store'), $this->dadosValidos([
            'foto' => $this->fotoPng(),
        ]));

        $resposta->assertRedirect(route('admin.professores.index'))
            ->assertSessionHas('sucesso');

        $criado = $this->professorPorCpf('529.982.247-25');

        $this->assertNotNull($criado);
        $this->assertSame(9, $criado['id']);
        $this->assertSame(['Yoga', 'Respiração'], $criado['especialidades']);
        $this->assertTrue($criado['ativo']);
        $this->assertStringStartsWith('img/professores/lucas-pereira-', $criado['foto']);
        $this->assertFileExists($this->pastaFotos.'/'.basename($criado['foto']));
    }

    #[Test]
    public function cadastra_professor_sem_foto_usa_a_imagem_padrao(): void
    {
        $this->post(route('admin.professores.store'), $this->dadosValidos())
            ->assertSessionHasNoErrors();

        $this->assertSame('img/professores/padrao.svg', $this->professorPorCpf('529.982.247-25')['foto']);
        $this->assertSame([], glob($this->pastaFotos.'/*'));
    }

    #[Test]
    public function aceita_cpf_so_com_numeros_e_guarda_formatado(): void
    {
        $this->post(route('admin.professores.store'), $this->dadosValidos(['cpf' => '52998224725']))
            ->assertSessionHasNoErrors();

        $this->assertNotNull($this->professorPorCpf('529.982.247-25'));
    }

    #[Test]
    public function exige_os_campos_obrigatorios(): void
    {
        $this->post(route('admin.professores.store'), [])
            ->assertSessionHasErrors(['nome', 'cpf', 'modalidade', 'especialidades', 'biografia', 'horario_trabalho']);

        $this->assertCount(8, json_decode(file_get_contents($this->arquivoJson), true));
    }

    #[Test]
    public function rejeita_cpf_invalido(): void
    {
        $this->post(route('admin.professores.store'), $this->dadosValidos(['cpf' => '111.111.111-11']))
            ->assertSessionHasErrors(['cpf' => 'O CPF informado não é válido.']);
    }

    #[Test]
    public function rejeita_cpf_ja_cadastrado(): void
    {
        $cpfExistente = json_decode(file_get_contents($this->arquivoJson), true)[0]['cpf'];

        $this->post(route('admin.professores.store'), $this->dadosValidos(['cpf' => $cpfExistente]))
            ->assertSessionHasErrors(['cpf' => 'Já existe um professor cadastrado com este CPF.']);
    }

    #[Test]
    public function rejeita_modalidade_fora_da_lista(): void
    {
        $this->post(route('admin.professores.store'), $this->dadosValidos(['modalidade' => 'Natação']))
            ->assertSessionHasErrors('modalidade');
    }

    #[Test]
    public function rejeita_arquivo_que_nao_e_imagem(): void
    {
        $this->post(route('admin.professores.store'), $this->dadosValidos([
            'foto' => UploadedFile::fake()->create('documento.pdf', 10, 'application/pdf'),
        ]))->assertSessionHasErrors('foto');

        $this->assertNull($this->professorPorCpf('529.982.247-25'));
    }

    #[Test]
    public function rejeita_foto_maior_que_2_mb(): void
    {
        $grande = $this->fotoPng();
        file_put_contents($grande->getPathname(), str_repeat('0', 3 * 1024 * 1024), FILE_APPEND);

        $this->post(route('admin.professores.store'), $this->dadosValidos(['foto' => $grande]))
            ->assertSessionHasErrors(['foto' => 'A foto pode ter no máximo 2 MB.']);
    }

    #[Test]
    public function mostra_o_formulario_de_cadastro_vazio(): void
    {
        $this->get(route('admin.professores.create'))
            ->assertOk()
            ->assertSee('Novo professor')
            ->assertSee('Cadastrar professor')
            ->assertSee('multipart/form-data', false)
            ->assertDontSee('Remover a foto atual');
    }

    #[Test]
    public function mostra_as_mensagens_de_erro_e_mantem_o_que_foi_digitado(): void
    {
        $this->followingRedirects()
            ->from(route('admin.professores.create'))
            ->post(route('admin.professores.store'), $this->dadosValidos(['cpf' => '111.111.111-11', 'nome' => 'Lucas Pereira']))
            ->assertOk()
            ->assertSee('Corrija os campos abaixo')
            ->assertSee('O CPF informado não é válido.')
            ->assertSee('value="Lucas Pereira"', false);
    }

    #[Test]
    public function mostra_o_formulario_de_edicao_preenchido(): void
    {
        $this->get(route('admin.professores.edit', 1))
            ->assertOk()
            ->assertSee('Carlos Silva')
            ->assertSee('Salvar alterações')
            ->assertSee('Remover a foto atual');
    }

    #[Test]
    public function edita_dados_e_mantem_a_foto_quando_nenhuma_nova_e_enviada(): void
    {
        $antes = $this->professorPorId(1);

        $this->put(route('admin.professores.update', 1), $this->dadosValidos([
            'cpf' => $antes['cpf'],
            'nome' => 'Carlos Silva Junior',
            'modalidade' => 'Funcional',
        ]))->assertRedirect(route('admin.professores.index'))
            ->assertSessionHas('sucesso');

        $depois = $this->professorPorId(1);

        $this->assertSame('Carlos Silva Junior', $depois['nome']);
        $this->assertSame('Funcional', $depois['modalidade']);
        $this->assertSame($antes['foto'], $depois['foto']);
        $this->assertCount(8, json_decode(file_get_contents($this->arquivoJson), true));
    }

    #[Test]
    public function trocar_a_foto_apaga_a_antiga(): void
    {
        $this->post(route('admin.professores.store'), $this->dadosValidos(['foto' => $this->fotoPng()]));
        $criado = $this->professorPorCpf('529.982.247-25');
        $fotoAntiga = $this->pastaFotos.'/'.basename($criado['foto']);
        $this->assertFileExists($fotoAntiga);

        $this->put(route('admin.professores.update', $criado['id']), $this->dadosValidos(['foto' => $this->fotoPng('nova.png')]))
            ->assertSessionHasNoErrors();

        $novo = $this->professorPorId($criado['id']);

        $this->assertNotSame($criado['foto'], $novo['foto']);
        $this->assertFileDoesNotExist($fotoAntiga);
        $this->assertFileExists($this->pastaFotos.'/'.basename($novo['foto']));
    }

    #[Test]
    public function remover_a_foto_volta_para_a_imagem_padrao(): void
    {
        $this->post(route('admin.professores.store'), $this->dadosValidos(['foto' => $this->fotoPng()]));
        $criado = $this->professorPorCpf('529.982.247-25');

        $this->put(route('admin.professores.update', $criado['id']), $this->dadosValidos(['remover_foto' => '1']));

        $this->assertSame('img/professores/padrao.svg', $this->professorPorId($criado['id'])['foto']);
        $this->assertFileDoesNotExist($this->pastaFotos.'/'.basename($criado['foto']));
    }

    #[Test]
    public function nao_deixa_editar_usando_o_cpf_de_outro_professor(): void
    {
        $cpfDoPrimeiro = $this->professorPorId(1)['cpf'];

        $this->put(route('admin.professores.update', 2), $this->dadosValidos(['cpf' => $cpfDoPrimeiro]))
            ->assertSessionHasErrors('cpf');
    }

    #[Test]
    public function exclui_o_professor_e_a_foto_enviada(): void
    {
        $this->post(route('admin.professores.store'), $this->dadosValidos(['foto' => $this->fotoPng()]));
        $criado = $this->professorPorCpf('529.982.247-25');
        $foto = $this->pastaFotos.'/'.basename($criado['foto']);

        $this->delete(route('admin.professores.destroy', $criado['id']))
            ->assertRedirect(route('admin.professores.index'))
            ->assertSessionHas('sucesso');

        $this->assertNull($this->professorPorId($criado['id']));
        $this->assertFileDoesNotExist($foto);
    }

    #[Test]
    public function devolve_404_para_professor_que_nao_existe(): void
    {
        $this->get(route('admin.professores.edit', 999))->assertNotFound();
        $this->put(route('admin.professores.update', 999), $this->dadosValidos())->assertNotFound();
        $this->delete(route('admin.professores.destroy', 999))->assertNotFound();
    }

    #[Test]
    public function professor_novo_aparece_na_api_publica_sem_expor_o_cpf(): void
    {
        $this->post(route('admin.professores.store'), $this->dadosValidos());

        $resposta = $this->getJson('/api/professores')->assertOk();

        $resposta->assertJsonCount(9)
            ->assertJsonFragment(['nome' => 'Lucas Pereira']);

        $this->assertStringNotContainsString('529.982.247-25', $resposta->getContent());
        $this->assertArrayNotHasKey('cpf', $resposta->json(0));
    }

    #[Test]
    public function professor_inativo_some_da_api_publica(): void
    {
        $this->put(route('admin.professores.update', 1), $this->dadosValidos([
            'cpf' => $this->professorPorId(1)['cpf'],
            'ativo' => '0',
        ]));

        $this->assertFalse($this->professorPorId(1)['ativo']);
        $this->getJson('/api/professores')->assertJsonCount(7);
        $this->getJson('/api/professores/1')->assertNotFound();
    }

    /**
     * @param  array<string, mixed>  $cpf
     */
    #[Test]
    #[DataProvider('cpfs')]
    public function valida_o_digito_verificador_do_cpf(string $valor, bool $esperado): void
    {
        $falhou = false;

        (new Cpf)->validate('cpf', $valor, function () use (&$falhou): void {
            $falhou = true;
        });

        $this->assertSame($esperado, ! $falhou);
    }

    /**
     * @return array<string, array{string, bool}>
     */
    public static function cpfs(): array
    {
        return [
            'valido com mascara' => ['529.982.247-25', true],
            'valido so numeros' => ['52998224725', true],
            'digito errado' => ['529.982.247-26', false],
            'todos iguais' => ['000.000.000-00', false],
            'curto demais' => ['123', false],
        ];
    }

    /**
     * @param  array<string, mixed>  $extra
     * @return array<string, mixed>
     */
    private function dadosValidos(array $extra = []): array
    {
        return array_merge([
            'nome' => 'Lucas Pereira',
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
        $caminho = $this->pastaBase.'/'.uniqid().'-'.$nome;
        file_put_contents($caminho, base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg=='
        ));

        return new UploadedFile($caminho, $nome, 'image/png', null, true);
    }

    /**
     * @return array<string, mixed>|null
     */
    private function professorPorId(int $id): ?array
    {
        foreach (json_decode(file_get_contents($this->arquivoJson), true) as $professor) {
            if ($professor['id'] === $id) {
                return $professor;
            }
        }

        return null;
    }

    /**
     * @return array<string, mixed>|null
     */
    private function professorPorCpf(string $cpf): ?array
    {
        foreach (json_decode(file_get_contents($this->arquivoJson), true) as $professor) {
            if ($professor['cpf'] === $cpf) {
                return $professor;
            }
        }

        return null;
    }
}
