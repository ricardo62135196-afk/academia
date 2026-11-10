<?php

namespace App\Support;

/**
 * Lê e grava os professores no arquivo JSON, com bloqueio de arquivo
 * para que duas gravações simultâneas não se sobreponham.
 */
class ProfessorRepository
{
    public function __construct(private readonly string $arquivo) {}

    /**
     * @return list<array<string, mixed>>
     */
    public function all(): array
    {
        if (! is_file($this->arquivo)) {
            return [];
        }

        $dados = json_decode((string) file_get_contents($this->arquivo), true);

        return is_array($dados) ? array_values($dados) : [];
    }

    /**
     * @return array<string, mixed>|null
     */
    public function find(int $id): ?array
    {
        foreach ($this->all() as $professor) {
            if (($professor['id'] ?? null) === $id) {
                return $professor;
            }
        }

        return null;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function findByCpf(string $cpf): ?array
    {
        foreach ($this->all() as $professor) {
            if (($professor['cpf'] ?? null) === $cpf) {
                return $professor;
            }
        }

        return null;
    }

    /**
     * @param  array<string, mixed>  $dados
     * @return array<string, mixed>
     */
    public function create(array $dados): array
    {
        $criado = [];

        $this->mutate(function (array $lista) use ($dados, &$criado): array {
            $proximoId = $lista === [] ? 1 : max(array_column($lista, 'id')) + 1;
            $criado = ['id' => $proximoId] + $dados;
            $lista[] = $criado;

            return $lista;
        });

        return $criado;
    }

    /**
     * @param  array<string, mixed>  $dados
     * @return array<string, mixed>|null
     */
    public function update(int $id, array $dados): ?array
    {
        $atualizado = null;

        $this->mutate(function (array $lista) use ($id, $dados, &$atualizado): array {
            foreach ($lista as $indice => $professor) {
                if (($professor['id'] ?? null) === $id) {
                    $atualizado = ['id' => $id] + $dados;
                    $lista[$indice] = $atualizado;
                }
            }

            return $lista;
        });

        return $atualizado;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function delete(int $id): ?array
    {
        $removido = null;

        $this->mutate(function (array $lista) use ($id, &$removido): array {
            return array_values(array_filter($lista, function (array $professor) use ($id, &$removido): bool {
                if (($professor['id'] ?? null) === $id) {
                    $removido = $professor;

                    return false;
                }

                return true;
            }));
        });

        return $removido;
    }

    /**
     * @param  callable(list<array<string, mixed>>): list<array<string, mixed>>  $alteracao
     */
    private function mutate(callable $alteracao): void
    {
        $diretorio = dirname($this->arquivo);

        if (! is_dir($diretorio)) {
            mkdir($diretorio, 0755, true);
        }

        $arquivo = fopen($this->arquivo, 'c+');
        flock($arquivo, LOCK_EX);

        $lista = json_decode((string) stream_get_contents($arquivo), true);
        $nova = $alteracao(is_array($lista) ? array_values($lista) : []);

        ftruncate($arquivo, 0);
        rewind($arquivo);
        fwrite($arquivo, json_encode($nova, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        fflush($arquivo);
        flock($arquivo, LOCK_UN);
        fclose($arquivo);
    }
}
