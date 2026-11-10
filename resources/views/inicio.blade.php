@extends('layouts.app')
@section('titulo', 'Início')
@section('conteudo')
<header class="topo">
  <a class="logo" href="{{ url('/') }}">Academia <span>Sete</span></a>
  <nav class="acoes" aria-label="Conta">
    <a class="link-topo" href="{{ url('/professores') }}">Professores</a>
    <a class="btn ciano" href="{{ url('/login') }}">Login</a>
    <a class="btn cheio" href="{{ url('/login') }}?aba=cadastro">Cadastro</a>
  </nav>
</header>
<section class="hero">
  <span class="sete" aria-hidden="true">7</span>
  <h1>Seu treino começa<br>às sete.</h1>
  <p>Musculação, funcional e acompanhamento de perto. Escolha quantos dias por semana cabem na sua rotina.</p>
  <a class="btn cheio" href="#planos">Ver planos</a>
</section>
<section class="secao historia">
  <div>
    <h2>Nossa história</h2>
    <p>Em 2015, a instrutora Marina e o ex-atleta Rafael abriram uma sala com meia dúzia de máquinas usadas. A primeira turma treinava às sete da manhã, e daí veio o nome.</p>
    <p>Hoje a Academia Sete tem três salas e mais de mil alunos, mas o ritmo é o mesmo: treino acompanhado e sem fila.</p>
  </div>
  <div class="ilustras">
    <figure class="rosa"><svg viewBox="0 0 120 60" fill="none" stroke="currentColor" stroke-width="5" stroke-linecap="round" role="img" aria-label="Halter"><path d="M10 20v20M22 12v36M98 12v36M110 20v20M22 30h76"/></svg><figcaption>Musculação</figcaption></figure>
    <figure class="ciano"><svg viewBox="0 0 120 60" fill="none" stroke="currentColor" stroke-width="5" stroke-linecap="round" role="img" aria-label="Kettlebell"><circle cx="60" cy="38" r="18"/><path d="M48 24c0-16 24-16 24 0"/></svg><figcaption>Funcional</figcaption></figure>
    <figure class="rosa"><svg viewBox="0 0 120 60" fill="none" stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round" role="img" aria-label="Batimentos"><path d="M5 30h30l8-22 14 44 10-22h48"/></svg><figcaption>Avaliação física</figcaption></figure>
  </div>
</section>
<!-- Botão do WhatsApp -->
    <a
        href="https://wa.me/5547996702281?text=Ol%C3%A1%21%20Gostaria%20de%20conhecer%20os%20planos%20da%20academia.%22"
        class="whatsapp-float" 
        target="_blank"
        rel="noopener noreferrer"
        aria-label="Fale com a academia pelo WhatsApp"
    >
        <span class="whatsapp-symbol">✆</span>
        <span>Fale conosco</span>
    </a>
<footer class="rodape">Academia Sete. Imagens ilustrativas.</footer>
@endsection
