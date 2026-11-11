<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('professores')) {
            Schema::create('professores', function (Blueprint $table): void {
                $table->increments('id');
                $table->string('nome')->nullable();
                $table->string('profissao')->nullable();
                $table->string('email')->nullable();
                $table->string('telefone')->nullable();
                $table->string('cpf', 14)->nullable()->unique();
                $table->string('modalidade', 100)->nullable();
                $table->json('especialidades')->nullable();
                $table->text('biografia')->nullable();
                $table->string('horario_trabalho', 100)->nullable();
                $table->string('foto')->nullable();
                $table->boolean('ativo')->default(false);
                $table->timestamps();
            });
        } else {
            Schema::table('professores', function (Blueprint $table): void {
                if (! Schema::hasColumn('professores', 'cpf')) {
                    $table->string('cpf', 14)->nullable()->unique();
                }
                if (! Schema::hasColumn('professores', 'modalidade')) {
                    $table->string('modalidade', 100)->nullable();
                }
                if (! Schema::hasColumn('professores', 'especialidades')) {
                    $table->json('especialidades')->nullable();
                }
                if (! Schema::hasColumn('professores', 'biografia')) {
                    $table->text('biografia')->nullable();
                }
                if (! Schema::hasColumn('professores', 'horario_trabalho')) {
                    $table->string('horario_trabalho', 100)->nullable();
                }
                if (! Schema::hasColumn('professores', 'foto')) {
                    $table->string('foto')->nullable();
                }
                if (! Schema::hasColumn('professores', 'ativo')) {
                    $table->boolean('ativo')->default(false);
                }
            });
        }

        $this->importLegacyProfessores();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('professores')) {
            return;
        }

        Schema::table('professores', function (Blueprint $table): void {
            $colunas = array_filter(
                ['cpf', 'modalidade', 'especialidades', 'biografia', 'horario_trabalho', 'foto', 'ativo'],
                fn (string $coluna): bool => Schema::hasColumn('professores', $coluna)
            );

            if ($colunas !== []) {
                $table->dropColumn($colunas);
            }
        });
    }

    private function importLegacyProfessores(): void
    {
        $arquivo = database_path('data/professores.json');

        if (! is_file($arquivo)) {
            throw new \RuntimeException('O arquivo legado database/data/professores.json não foi encontrado.');
        }

        $professores = json_decode((string) file_get_contents($arquivo), true, flags: JSON_THROW_ON_ERROR);

        foreach ($professores as $professor) {
            if (DB::table('professores')->where('cpf', $professor['cpf'])->exists()) {
                continue;
            }

            DB::table('professores')->insert([
                'nome' => $professor['nome'],
                'cpf' => $professor['cpf'],
                'modalidade' => $professor['modalidade'],
                'especialidades' => json_encode($professor['especialidades'], JSON_THROW_ON_ERROR),
                'biografia' => $professor['biografia'],
                'horario_trabalho' => $professor['horario_trabalho'],
                'foto' => $professor['foto'],
                'ativo' => $professor['ativo'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
};
