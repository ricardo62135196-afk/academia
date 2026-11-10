@extends('layouts.painel')
@section('titulo', 'Meu Objetivo')
@section('pagina')
<section class="bloco">
  <h2>Bloco de notas</h2>
  <form id="formNota" class="linha">
    <label>Anotação<input id="notaTexto" type="text" required maxlength="120"></label>
    <label>Dia limite<input id="notaPrazo" type="date" required></label>
    <button class="btn cheio" type="submit">Salvar anotação</button>
  </form>
  <ul id="listaNotas" class="notas"></ul>
</section>
<div class="duas">
  <section class="bloco">
    <h2>Tempo de treino</h2>
    <p class="tempo" id="tempo" aria-live="off">00:00:00</p>
    <div class="linha">
      <button class="btn cheio" id="iniciar" type="button">Iniciar</button>
      <button class="btn ciano" id="pausar" type="button">Pausar</button>
      <button class="btn" id="zerar" type="button">Zerar</button>
    </div>
  </section>
  <section class="bloco">
    <h2>Calculadora de IMC</h2>
    <form id="formImc">
      <label>Peso (kg)<input id="peso" type="number" min="20" max="300" step="0.1" required></label>
      <label>Altura (cm)<input id="altura" type="number" min="100" max="250" required></label>
      <button class="btn cheio" type="submit">Calcular IMC</button>
    </form>
    <p id="resultadoImc" class="destaque" role="status"></p>
  </section>
</div>
@endsection
