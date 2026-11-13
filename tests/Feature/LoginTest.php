<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function tela_de_login_envia_credenciais_para_o_servidor(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertSee('action="'.route('login.store').'"', false)
            ->assertSee('name="email"', false)
            ->assertSee('name="password"', false);
    }

    #[Test]
    public function login_autentica_usuario_e_mantem_sessao_na_tela_de_professores(): void
    {
        $usuario = User::factory()->create([
            'email' => 'marina@example.com',
            'password' => Hash::make('senha-segura-123'),
        ]);

        $this->post(route('login.store'), [
            'email' => 'marina@example.com',
            'password' => 'senha-segura-123',
        ])->assertRedirect('/principal');

        $this->assertAuthenticatedAs($usuario);

        $this->get('/professores')
            ->assertOk()
            ->assertSee('Minha conta')
            ->assertSee('Sair')
            ->assertDontSee('href="'.url('/login').'"', false);

        $this->assertAuthenticatedAs($usuario);
    }

    #[Test]
    public function login_rejeita_credenciais_invalidas(): void
    {
        User::factory()->create([
            'email' => 'marina@example.com',
            'password' => Hash::make('senha-segura-123'),
        ]);

        $this->post(route('login.store'), [
            'email' => 'marina@example.com',
            'password' => 'senha-incorreta',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }
}
