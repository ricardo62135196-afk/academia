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
    @if ($errors->any())
      <div class="alerta erro" role="alert">
        <ul>
          @foreach ($errors->all() as $mensagem)
            <li>{{ $mensagem }}</li>
          @endforeach
        </ul>
      </div>
    @endif
    <form data-painel="login" method="POST" action="{{ route('login.store') }}">
      @csrf
      <label>E-mail<input type="email" name="email" value="{{ old('email') }}" required autocomplete="email"></label>
      @error('email')
        <p class="erro-campo">{{ $message }}</p>
      @enderror
      <label>Senha<input type="password" name="password" required autocomplete="current-password"></label>
      @error('password')
        <p class="erro-campo">{{ $message }}</p>
      @enderror
      <button class="btn cheio" type="submit">Entrar</button>
    </form>
    <form data-painel="cadastro" method="POST" action="{{ route('cadastro.store') }}" hidden>
      @csrf
      <label>Nome completo<input type="text" name="name" value="{{ old('name') }}" required maxlength="255" autocomplete="name"></label>
      @error('name')
        <p class="erro-campo">{{ $message }}</p>
      @enderror
      <label>E-mail<input type="email" name="email" value="{{ old('email') }}" required maxlength="255" autocomplete="email"></label>
      @error('email')
        <p class="erro-campo">{{ $message }}</p>
      @enderror
      <label>Senha<input type="password" name="password" required minlength="8" maxlength="255" autocomplete="new-password"></label>
      @error('password')
        <p class="erro-campo">{{ $message }}</p>
      @enderror
      <button class="btn cheio" type="submit">Criar conta</button>
    </form>
  </div>
</div>
@endsection
