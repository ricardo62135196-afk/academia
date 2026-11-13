<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AlunosCadastroTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function tela_de_cadastro_de_alunos_exibe_campo_de_senha(): void
    {
        $this->get(route('alunos.index'))
            ->assertOk()
            ->assertSee('name="password"', false);
    }

    #[Test]
    public function cadastro_cria_aluno_com_senha_em_hash(): void
    {
        $this->post(route('alunos.store'), [
            'nome' => 'Marina Costa',
            'email' => 'marina@example.com',
            'telefone' => '11999999999',
            'password' => 'senha-segura-123',
        ])->assertRedirect(route('alunos.index'))
            ->assertSessionHas('sucesso');

        $aluno = DB::table('alunos')->where('email', 'marina@example.com')->first();

        $this->assertNotNull($aluno);
        $this->assertNotSame('senha-segura-123', $aluno->password);
        $this->assertTrue(Hash::check('senha-segura-123', $aluno->password));
    }

    #[Test]
    public function cadastro_rejeita_senha_curta_sem_criar_aluno(): void
    {
        $this->post(route('alunos.store'), [
            'nome' => 'Marina Costa',
            'email' => 'marina@example.com',
            'telefone' => '11999999999',
            'password' => '1234567',
        ])->assertSessionHasErrors('password');

        $this->assertDatabaseCount('alunos', 0);
    }
}
