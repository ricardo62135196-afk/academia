@extends('layouts.painel')
@section('titulo', 'Minha Conta')
@section('pagina')
<section class="bloco">
  <h2>Informações pessoais</h2>
  <form data-simulacao>
    <label>Nome completo<input type="text" value="Aluno Exemplo" required></label>
    <label>E-mail<input type="email" value="aluno@@exemplo.com" required></label>
    <label>Telefone<input type="tel" placeholder="(47) 90000-0000"></label>
    <label>Data de nascimento<input type="date"></label>
    <label>Nova senha<input type="password" minlength="6" autocomplete="new-password"></label>
    <button class="btn cheio" type="submit">Salvar alterações</button>
  </form>
</section>
@endsection
