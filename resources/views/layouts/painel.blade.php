@extends('layouts.app')
@section('conteudo')
@php
$menu = ['principal' => 'Principal', 'modalidades' => 'Modalidades', 'professores' => 'Professores', 'admin/professores' => 'Gerenciar Professores', 'meu-objetivo' => 'Meu Objetivo', 'dicas-de-treino' => 'Dicas de Treino', 'imc' => 'Calculadora IMC', 'agendamento' => 'Agendamento', 'mural' => 'Mural', 'atendimento' => 'Atendimento', 'opcoes' => 'Opções', 'minha-conta' => 'Minha Conta', 'notificacoes' => 'Notificações'];
@endphp
<div class="shell" id="shell">
  <aside class="sidebar" id="sidebar">
    <a class="logo" href="{{ url('/principal') }}">Academia <span>Sete</span></a>
    <nav aria-label="Menu principal">
      @foreach ($menu as $rota => $nome)
        <a href="{{ url('/' . $rota) }}" @if (request()->is($rota, $rota . '/*')) aria-current="page" @endif>{{ $nome }}</a>
      @endforeach
    </nav>
    @auth
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button class="btn ciano" type="submit">Sair</button>
      </form>
    @endauth
  </aside>
  <main class="conteudo">
    <div class="barra">
      <button class="btn ciano" id="toggleMenu" type="button" aria-controls="sidebar">Menu</button>
      <h1>@yield('titulo')</h1>
    </div>
    @yield('pagina')
  </main>
</div>
@endsection
