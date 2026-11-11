@extends('layouts.painel')

@section('titulo', 'Cadastro de professores')

@section('pagina')
    <p>Preencha os dados abaixo para cadastrar um professor.</p>

    @if (session('sucesso'))
        <div class="alerta sucesso" role="status">{{ session('sucesso') }}</div>
    @endif

    @if ($errors->any())
        <div class="alerta erro" role="alert">
            <strong>Não foi possível cadastrar o professor:</strong>
            <ul>
                @foreach ($errors->all() as $mensagem)
                    <li>{{ $mensagem }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="form-card" aria-label="Dados do professor">
        <form method="POST" action="{{ route('professores.store') }}">
            @csrf

            <label for="nome">Nome</label>
            <input
                type="text"
                name="nome"
                id="nome"
                value="{{ old('nome') }}"
                maxlength="255"
                autocomplete="name"
                required
            >
            @error('nome')
                <p class="erro-campo">{{ $message }}</p>
            @enderror

            <label for="profissao">Profissão</label>
            <input
                type="text"
                name="profissao"
                id="profissao"
                value="{{ old('profissao') }}"
                maxlength="255"
                autocomplete="organization-title"
                required
            >
            @error('profissao')
                <p class="erro-campo">{{ $message }}</p>
            @enderror

            <label for="email">E-mail</label>
            <input
                type="email"
                name="email"
                id="email"
                value="{{ old('email') }}"
                maxlength="255"
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
                maxlength="255"
                autocomplete="tel"
                required
            >
            @error('telefone')
                <p class="erro-campo">{{ $message }}</p>
            @enderror

            <button class="btn cheio" type="submit">Cadastrar professor</button>
        </form>
    </section>
@endsection
