<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function tela_de_cadastro_envia_os_dados_para_o_servidor(): void
    {
        $this->get('/login?aba=cadastro')
            ->assertOk()
            ->assertSee('action="'.route('cadastro.store').'"', false)
            ->assertSee('name="password"', false);
    }

    #[Test]
    public function cadastro_cria_conta_com_senha_em_hash_e_autentica_usuario(): void
    {
        $this->post(route('cadastro.store'), [
            'name' => 'Marina Costa',
            'email' => 'marina@example.com',
            'password' => 'senha-segura-123',
        ])->assertRedirect('/principal');

        $usuario = User::query()->where('email', 'marina@example.com')->firstOrFail();

        $this->assertModelExists($usuario);
        $this->assertAuthenticatedAs($usuario);
        $this->assertNotSame('senha-segura-123', $usuario->password);
        $this->assertTrue(Hash::check('senha-segura-123', $usuario->password));
    }

    #[Test]
    public function cadastro_rejeita_email_duplicado(): void
    {
        User::factory()->create(['email' => 'marina@example.com']);

        $this->post(route('cadastro.store'), [
            'name' => 'Outra Marina',
            'email' => 'marina@example.com',
            'password' => 'senha-segura-123',
        ])->assertSessionHasErrors('email');

        $this->assertDatabaseCount('users', 1);
    }

    #[Test]
    public function cadastro_rejeita_senha_curta_sem_criar_conta(): void
    {
        $this->post(route('cadastro.store'), [
            'name' => 'Marina Costa',
            'email' => 'marina@example.com',
            'password' => '1234567',
        ])->assertSessionHasErrors('password');

        $this->assertDatabaseCount('users', 0);
    }
}
