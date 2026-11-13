@extends('layouts.painel')

@section('titulo', 'Cadastro de alunos')

@section('pagina')
    <p>Preencha os dados abaixo para cadastrar um aluno.</p>

    @if (session('sucesso'))
        <div class="alerta sucesso" role="status">{{ session('sucesso') }}</div>
    @endif

    @if ($errors->any())
        <div class="alerta erro" role="alert">
            <strong>Não foi possível cadastrar o aluno:</strong>
            <ul>
                @foreach ($errors->all() as $mensagem)
                    <li>{{ $mensagem }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="form-card" aria-label="Dados do aluno">
        <form method="POST" action="{{ route('alunos.store') }}">
            @csrf

            <label for="nome">Nome</label>
            <input
                type="text"
                name="nome"
                id="nome"
                value="{{ old('nome') }}"
                maxlength="100"
                autocomplete="name"
                required
            >
            @error('nome')
                <p class="erro-campo">{{ $message }}</p>
            @enderror

            <label for="email">E-mail</label>
            <input
                type="email"
                name="email"
                id="email"
                value="{{ old('email') }}"
                maxlength="30"
                autocomplete="email"
                required
            >
            @error('email')
                <p class="erro-campo">{{ $message }}</p>
            @enderror

            <label for="telefone">Telefone</label>
            <input
                type="tel"
                name="telefone"
                id="telefone"
                value="{{ old('telefone') }}"
                maxlength="20"
                autocomplete="tel"
                required
            >
            @error('telefone')
                <p class="erro-campo">{{ $message }}</p>
            @enderror

            <label for="password">Senha</label>
            <input
                type="password"
                name="password"
                id="password"
                minlength="8"
                maxlength="255"
                autocomplete="new-password"
                required
            >
            @error('password')
                <p class="erro-campo">{{ $message }}</p>
            @enderror

            <button class="btn cheio" type="submit">Cadastrar aluno</button>
        </form>
    </section>
@endsection