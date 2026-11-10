@extends('layouts.painel')
@section('titulo', 'Dicas de Treino')
@section('pagina')
@php
$grupos = ['Bíceps', 'Pernas', 'Braços', 'Costas', 'Ombros', 'Tríceps'];
$niveis = ['para iniciantes', 'avançado', 'em casa'];
@endphp
<p>Vídeos de exemplo por grupo muscular. Os links abrem uma busca e podem ser trocados por vídeos específicos.</p>
<div class="duas">
  @foreach ($grupos as $g)
    <section class="bloco">
      <h2>{{ $g }}</h2>
      <ul class="links">
        @foreach ($niveis as $n)
          <li><a href="https://www.youtube.com/results?search_query={{ urlencode('treino de ' . $g . ' ' . $n) }}" target="_blank" rel="noopener">Treino de {{ mb_strtolower($g) }} {{ $n }}</a></li>
        @endforeach
      </ul>
    </section>
  @endforeach
</div>
@endsection
