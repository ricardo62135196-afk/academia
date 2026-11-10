@extends('layouts.app')
@section('titulo', 'Entrar')
@section('conteudo')
<div class="auth">
  <div class="caixa">
    <a class="logo" href="{{ url('/') }}">Academia <span>Sete</span></a>
    <p id="planoEscolhido" class="destaque" hidden></p>
    <div class="abas" role="tablist">
      <button type="button" role="tab" data-aba="login">Login</button>
      <button type="button" role="tab" data-aba="cadastro">Cadastro</button>
    </div>
    <form data-painel="login" data-next="{{ url('/principal') }}">
      <label>E-mail<input type="email" required autocomplete="email"></label>
      <label>Senha<input type="password" required autocomplete="current-password"></label>
      <button class="btn cheio" type="submit">Entrar</button>
    </form>
    <form data-painel="cadastro" data-next="{{ url('/principal') }}" hidden>
      <label>Nome completo<input type="text" required autocomplete="name"></label>
      <label>E-mail<input type="email" required autocomplete="email"></label>
      <label>Senha<input type="password" required minlength="6" autocomplete="new-password"></label>
      <button class="btn cheio" type="submit">Criar conta</button>
    </form>
  </div>
</div>
@endsection
