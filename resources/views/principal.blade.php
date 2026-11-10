@extends('layouts.painel')
@section('titulo', 'Principal')
@section('pagina')
<p>Escolha um plano para ver os detalhes e gerar o pagamento. Quer conhecer as atividades? <a class="destaque" href="{{ url('/modalidades') }}">Veja nossas modalidades</a>.</p>
@include('partials.planos', ['logado' => true])
@endsection
