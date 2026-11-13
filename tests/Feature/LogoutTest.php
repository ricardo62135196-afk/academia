<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function painel_exibe_opcao_de_logout(): void
    {
        $usuario = User::factory()->create();

        $this->actingAs($usuario)
            ->get('/principal')
            ->assertOk()
            ->assertSee('Sair')
            ->assertSee('action="'.route('logout').'"', false);
    }

    #[Test]
    public function tela_de_professores_preserva_sessao_e_exibe_menu_de_usuario_autenticado(): void
    {
        $usuario = User::factory()->create();

        $this->actingAs($usuario)
            ->get('/professores')
            ->assertOk()
            ->assertSee('Sair')
            ->assertSee('action="'.route('logout').'"', false);

        $this->assertAuthenticatedAs($usuario);
    }

    #[Test]
    public function logout_invalida_sessao_e_redireciona_para_inicio(): void
    {
        $this->actingAs(User::factory()->create())
            ->post(route('logout'))
            ->assertRedirect('/');

        $this->assertGuest();
    }
}
