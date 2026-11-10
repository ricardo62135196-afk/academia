@extends('layouts.painel')
@section('titulo', 'Opções')
@section('pagina')
<section class="bloco">
  <h2>Aparência</h2>
  <label>Tema
    <select id="tema"><option value="escuro">Escuro</option><option value="claro">Claro</option></select>
  </label>
  <label>Tamanho da letra
    <input id="fonte" type="range" min="14" max="22" step="1">
  </label>
  <label class="check"><input id="contraste" type="checkbox"> Alto contraste</label>
  <label class="check"><input id="reduzir" type="checkbox"> Reduzir animações</label>
  <button class="btn ciano" id="restaurar" type="button">Restaurar padrão</button>
</section>
@endsection
