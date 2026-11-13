@extends('layouts.app')
@section('conteudo')
<header class="topo">
  <a class="logo" href="{{ url('/') }}">Academia <span>Sete</span></a>
  <nav class="acoes" aria-label="Menu público">
    <a class="link-topo" href="{{ url('/') }}">Início</a>
    <a class="link-topo" href="{{ url('/professores') }}" @if (request()->is('professores*')) aria-current="page" @endif>Professores</a>
    @auth
      <a class="btn ciano" href="{{ url('/principal') }}">Minha conta</a>
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button class="btn cheio" type="submit">Sair</button>
      </form>
    @else
      <a class="btn ciano" href="{{ url('/login') }}">Login</a>
      <a class="btn cheio" href="{{ url('/login') }}?aba=cadastro">Cadastro</a>
    @endauth
  </nav>
</header>
<main class="secao">
  @yield('pagina')
</main>
<footer class="rodape">Academia Sete. Imagens ilustrativas.</footer>
@endsection
