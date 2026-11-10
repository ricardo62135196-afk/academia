@extends('layouts.painel')
@section('titulo', 'Mural')
@section('pagina')
<section class="bloco">
  <h2>Adicionar foto</h2>
  <form id="formFoto" class="linha">
    <label>Foto<input id="arquivo" type="file" accept="image/*" required></label>
    <label>Legenda (opcional)<input id="legenda" type="text" maxlength="80"></label>
    <button class="btn cheio" type="submit">Publicar foto</button>
  </form>
  <p>Por enquanto as fotos ficam só neste navegador. Com o back-end, elas aparecerão para todos os alunos.</p>
</section>
<div id="mural" class="mural"></div>
@endsection
