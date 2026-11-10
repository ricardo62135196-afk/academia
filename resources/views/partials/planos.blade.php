@php
$planos = [
  [
    'nome' => 'Sete 1', 'freq' => '1 dia por semana', 'preco' => '59,90',
    'info' => 'Adaptação com musculação leve, 60 minutos por treino.',
    'modalidades' => ['Musculação'],
    'inclui' => ['Avaliação física de boas-vindas', 'Ficha de treino montada por um professor', 'Acompanhamento no salão de musculação'],
  ],
  [
    'nome' => 'Sete 2', 'freq' => '2 dias por semana', 'preco' => '89,90',
    'info' => 'Divisão A/B: superiores e inferiores, 75 minutos por treino.',
    'modalidades' => ['Musculação', 'Funcional'],
    'inclui' => ['Tudo do plano Sete 1', 'Aulas coletivas de Funcional', 'Reavaliação física a cada 3 meses'],
  ],
  [
    'nome' => 'Sete 3', 'freq' => '3 dias por semana', 'preco' => '119,90',
    'info' => 'Divisão A/B/C com cardio no final, 90 minutos por treino.',
    'modalidades' => ['Musculação', 'Funcional', 'Pilates', 'Spinning'],
    'inclui' => ['Tudo do plano Sete 2', 'Aulas de Pilates e Spinning incluídas', 'Reavaliação física todo mês'],
  ],
];
$logado = $logado ?? false;
@endphp
<div class="planos">
  @foreach ($planos as $i => $p)
    <a class="plano" href="{{ $logado ? url('/atendimento') . '?plano=' . ($i + 1) . '#pagamento' : url('/login') . '?plano=' . ($i + 1) }}">
      <h3>{{ $p['nome'] }}</h3>
      <p>{{ $p['freq'] }}</p>
      <p class="preco">R$ {{ $p['preco'] }}<small>/mês</small></p>
      @if ($logado)<p class="info">{{ $p['info'] }}</p>@endif
      <p class="rotulo">Modalidades incluídas</p>
      <ul class="chips">
        @foreach ($p['modalidades'] as $m)<li>{{ $m }}</li>@endforeach
      </ul>
      <p class="rotulo">O que está incluso</p>
      <ul class="beneficios">
        @foreach ($p['inclui'] as $item)<li>{{ $item }}</li>@endforeach
      </ul>
      @if ($logado)<span class="btn cheio">Gerar pagamento</span>@endif
    </a>
  @endforeach
</div>
