<?php
 
namespace App\Http\Controllers;
 
class AcademiaController extends Controller
{
    private function listaModalidades(): array
    {
        return [
            [
                'id' => 1,
                'nome' => 'Musculação',
                'descricao' => 'Treinamento de força e resistência.',
                'sobre' => 'Treino com pesos, máquinas e peso livre para ganhar força, massa muscular e condicionamento. Cada aluno recebe uma ficha montada por um professor e ajustada conforme a evolução.',
                'beneficios' => ['Ganho de força e massa muscular', 'Melhora da postura e da saúde dos ossos', 'Maior gasto calórico, até em repouso'],
                'publico' => 'Todas as idades, do iniciante ao avançado',
                'nivel' => 'Iniciante a avançado',
                'duracao' => '60 a 90 minutos',
                'intensidade' => 'Moderada a alta',
                'levar' => ['Toalha', 'Garrafa de água', 'Roupa e tênis confortáveis'],
                'planos' => ['Sete 1', 'Sete 2', 'Sete 3']
            ],
            [
                'id' => 2,
                'nome' => 'Pilates',
                'descricao' => 'Postura e flexibilidade.',
                'sobre' => 'Exercícios de controle, respiração e alongamento que fortalecem o centro do corpo e melhoram a postura. As aulas são em turmas pequenas, com atenção individual.',
                'beneficios' => ['Mais flexibilidade e mobilidade', 'Alívio de tensões e dores nas costas', 'Fortalecimento do abdômen e da coluna'],
                'publico' => 'Adultos e idosos, inclusive gestantes (com liberação médica)',
                'nivel' => 'Iniciante a intermediário',
                'duracao' => '50 minutos',
                'intensidade' => 'Leve a moderada',
                'levar' => ['Meias antiderrapantes', 'Roupa que permita movimento', 'Toalha pequena'],
                'planos' => ['Sete 3']
            ],
            [
                'id' => 3,
                'nome' => 'Funcional',
                'descricao' => 'Condicionamento físico.',
                'sobre' => 'Circuitos que usam o peso do corpo, elásticos, kettlebells e cordas para treinar força, agilidade e resistência ao mesmo tempo. As aulas são coletivas e dinâmicas, com opções mais leves ou mais intensas.',
                'beneficios' => ['Melhor condicionamento físico', 'Mais coordenação e equilíbrio', 'Alta queima calórica'],
                'publico' => 'Adultos de todos os níveis',
                'nivel' => 'Iniciante a avançado',
                'duracao' => '45 a 60 minutos',
                'intensidade' => 'Moderada a alta',
                'levar' => ['Tênis fechado', 'Garrafa de água', 'Toalha'],
                'planos' => ['Sete 2', 'Sete 3']
            ],
            [
                'id' => 4,
                'nome' => 'Spinning',
                'descricao' => 'Exercícios em bicicleta.',
                'sobre' => 'Aula de bicicleta indoor ao ritmo da música, com subidas, sprints e treinos intervalados. Cada aluno ajusta a carga da própria bicicleta ao seu nível.',
                'beneficios' => ['Fortalece o coração e os pulmões', 'Ótima para queima de gordura', 'Baixo impacto nas articulações'],
                'publico' => 'Adultos de todos os níveis, sem exigir experiência com bicicleta',
                'nivel' => 'Iniciante a avançado',
                'duracao' => '45 minutos',
                'intensidade' => 'Alta',
                'levar' => ['Toalha', 'Garrafa de água', 'Tênis (ou sapatilha própria)', 'Roupa leve'],
                'planos' => ['Sete 3']
            ]
        ];
    }
 
    private function listaPlanos(): array
    {
        return [
            [
                'id' => 1,
                'nome' => 'Básico',
                'preco' => 79.90
            ],
            [
                'id' => 2,
                'nome' => 'Intermediário',
                'preco' => 119.90
            ],
            [
                'id' => 3,
                'nome' => 'Premium',
                'preco' => 159.90
            ]
        ];
    }
 
    public function modalidades()
    {
        // A lista traz só o resumo; os detalhes vêm em /api/modalidades/{id}
        $resumo = array_map(fn ($m) => [
            'id' => $m['id'],
            'nome' => $m['nome'],
            'descricao' => $m['descricao']
        ], $this->listaModalidades());

        return response()->json($resumo, 200, [], JSON_UNESCAPED_UNICODE);
    }
 
    public function modalidade(int $id)
    {
        foreach ($this->listaModalidades() as $modalidade) {
            if ($modalidade['id'] === $id) {
                $modalidade['professores'] = array_values(array_map(fn ($p) => [
                    'id' => $p['id'],
                    'nome' => $p['nome'],
                    'horario_trabalho' => $p['horario_trabalho']
                ], array_filter(
                    $this->professoresAtivos(),
                    fn ($p) => $p['modalidade'] === $modalidade['nome']
                )));

                return response()->json($modalidade, 200, [], JSON_UNESCAPED_UNICODE);
            }
        }
 
        return response()->json([
            'mensagem' => 'Modalidade não encontrada.'
        ], 404, [], JSON_UNESCAPED_UNICODE);
    }
 
    public function planos()
    {
        return response()->json($this->listaPlanos());
    }
 
    public function plano(int $id)
    {
        foreach ($this->listaPlanos() as $plano) {
            if ($plano['id'] === $id) {
                return response()->json($plano);
            }
        }
 
        return response()->json([
            'mensagem' => 'Plano não encontrado.'
        ], 404);
    }
 
   
private function professoresAtivos(): array
{
    $todos = app(\App\Support\ProfessorRepository::class)->all();
    $ativos = array_values(array_filter($todos, fn ($p) => ($p['ativo'] ?? false) === true));

    // Dados públicos: o CPF e o status interno nunca saem pela API
    return array_map(function ($p) {
        unset($p['cpf'], $p['ativo']);
        $p['foto'] = asset($p['foto']);
        return $p;
    }, $ativos);
}

public function professores()
{
    return response()->json($this->professoresAtivos(), 200, [], JSON_UNESCAPED_UNICODE);
}

public function professor(int $id)
{
    foreach ($this->professoresAtivos() as $professor) {
        if ($professor['id'] === $id) {
            return response()->json($professor, 200, [], JSON_UNESCAPED_UNICODE);
        }
    }

    return response()->json(['mensagem' => 'Professor não encontrado.'], 404, [], JSON_UNESCAPED_UNICODE);
}

public function horarios($id)
{
    $horarios = [
        1 => [
            'modalidade' => 'Musculação',
            'professor' => 'Carlos Silva',
            'horarios' => [
                'Segunda, quarta e sexta - 07:00',
                'Terça e quinta - 18:00'
            ]
        ],
        2 => [
            'modalidade' => 'Pilates',
            'professor' => 'Ana Oliveira',
            'horarios' => [
                'Terça e quinta - 08:00',
                'Terça e quinta - 17:00'
            ]
        ],
        3 => [
            'modalidade' => 'Funcional',
            'professor' => 'Mariana Costa',
            'horarios' => [
                'Segunda e quarta - 18:30',
                'Sexta - 19:00'
            ]
        ],
        4 => [
            'modalidade' => 'Spinning',
            'professor' => 'Ricardo Souza',
            'horarios' => [
                'Terça e quinta - 19:00',
                'Sábado - 09:00'
            ]
        ]
    ];
 
    if (!isset($horarios[$id])) {
        return response()->json([
            'erro' => 'Modalidade não encontrada'
        ], 404);
    }
 
    return response()->json(
        $horarios[$id],
        200,
        [],
        JSON_UNESCAPED_UNICODE
    );
}
 
}