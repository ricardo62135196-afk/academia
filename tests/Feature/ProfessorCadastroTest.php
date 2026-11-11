<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ProfessorCadastroTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('professores', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('nome')->nullable();
            $table->string('profissao')->nullable();
            $table->string('email')->nullable();
            $table->string('telefone')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    public function test_formulario_de_cadastro_de_professor_e_exibido(): void
    {
        $this->get(route('professores.create'))
            ->assertOk()
            ->assertSee('Cadastro de professores')
            ->assertSee('Cadastrar professor')
            ->assertSee('name="_token"', false);
    }

    public function test_envio_valido_cadastra_professor_no_banco(): void
    {
        $dados = [
            'nome' => 'Ricardo Souza',
            'profissao' => 'Personal trainer',
            'email' => 'ricardo@example.com',
            'telefone' => '4799999999999',
        ];

        $this->post(route('professores.store'), $dados)
            ->assertRedirect(route('professores.create'))
            ->assertSessionHas('sucesso', 'Professor cadastrado com sucesso.');

        $this->assertDatabaseHas('professores', $dados);
        $this->assertDatabaseCount('professores', 1);
    }

    public function test_campos_obrigatorios_invalidos_nao_cadastram_professor(): void
    {
        $this->from(route('professores.create'))
            ->post(route('professores.store'), [
                'nome' => '',
                'profissao' => '',
                'email' => 'email-invalido',
                'telefone' => '',
            ])
            ->assertRedirect(route('professores.create'))
            ->assertSessionHasErrors(['nome', 'profissao', 'email', 'telefone']);

        $this->assertDatabaseCount('professores', 0);
    }

    public function test_rota_de_perfil_publico_continua_disponivel(): void
    {
        $this->get('/professores/1')
            ->assertOk()
            ->assertSee('perfil-professor');
    }
}
