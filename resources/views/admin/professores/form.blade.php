@extends('layouts.painel')
@section('titulo', $professor ? 'Editar professor' : 'Novo professor')
@section('pagina')
@php
    $editando = $professor !== null;
    $especialidades = old('especialidades', $professor['especialidades'] ?? []);
    $especialidades = is_array($especialidades) ? implode("\n", $especialidades) : $especialidades;
    $fotoAtual = $professor?->foto_url ?? asset(config('academia.foto_padrao'));
@endphp

<a class="voltar" href="{{ route('admin.professores.index') }}">&larr; Voltar para a lista</a>

@if ($errors->any())
    <div class="alerta erro" role="alert">
        <strong>Corrija os campos abaixo:</strong>
        <ul>
            @foreach ($errors->all() as $mensagem)
                <li>{{ $mensagem }}</li>
            @endforeach
        </ul>
    </div>
@endif

<section class="form-card larga">
    <form method="POST" enctype="multipart/form-data"
          action="{{ $editando ? route('admin.professores.update', $professor['id']) : route('admin.professores.store') }}">
        @csrf
        @if ($editando)
            @method('PUT')
        @endif

        <div class="foto-campo">
            <img id="previa-foto" src="{{ $fotoAtual }}" alt="Foto do professor" width="140" height="140">
            <div>
                <label for="foto">Foto do professor</label>
                <input type="file" name="foto" id="foto" accept="image/jpeg,image/png,image/webp">
                <p class="ajuda">JPG, PNG ou WebP, até 2 MB. Prefira uma foto em pé ou quadrada, com o rosto na parte de cima.</p>
                @error('foto')<p class="erro-campo">{{ $message }}</p>@enderror
                @if ($editando)
                    <label class="check">
                        <input type="checkbox" name="remover_foto" value="1" @checked(old('remover_foto'))>
                        Remover a foto atual (usar a imagem padrão)
                    </label>
                @endif
            </div>
        </div>

        <label for="nome">Nome</label>
        <input type="text" name="nome" id="nome" value="{{ old('nome', $professor['nome'] ?? '') }}" maxlength="255" required>
        @error('nome')<p class="erro-campo">{{ $message }}</p>@enderror

        <label for="profissao">Profissão</label>
        <input type="text" name="profissao" id="profissao" value="{{ old('profissao', $professor['profissao'] ?? '') }}" maxlength="255">
        @error('profissao')<p class="erro-campo">{{ $message }}</p>@enderror

        <div class="duas-colunas">
            <div>
                <label for="email">E-mail</label>
                <input type="email" name="email" id="email" value="{{ old('email', $professor['email'] ?? '') }}" maxlength="255" autocomplete="email">
                @error('email')<p class="erro-campo">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="telefone">Telefone</label>
                <input type="tel" name="telefone" id="telefone" value="{{ old('telefone', $professor['telefone'] ?? '') }}" maxlength="255" autocomplete="tel">
                @error('telefone')<p class="erro-campo">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="duas-colunas">
            <div>
                <label for="cpf">CPF</label>
                <input type="text" name="cpf" id="cpf" inputmode="numeric" maxlength="14" placeholder="000.000.000-00"
                       value="{{ old('cpf', $professor['cpf'] ?? '') }}" required>
                @error('cpf')<p class="erro-campo">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="modalidade">Modalidade</label>
                <select name="modalidade" id="modalidade" required>
                    <option value="">Escolha...</option>
                    @foreach (config('academia.modalidades') as $modalidade)
                        <option value="{{ $modalidade }}" @selected(old('modalidade', $professor['modalidade'] ?? '') === $modalidade)>{{ $modalidade }}</option>
                    @endforeach
                </select>
                @error('modalidade')<p class="erro-campo">{{ $message }}</p>@enderror
            </div>
        </div>

        <label for="horario_trabalho">Horário de trabalho</label>
        <input type="text" name="horario_trabalho" id="horario_trabalho" maxlength="100"
               placeholder="Ex.: Segunda a sexta, 06:00 às 14:00"
               value="{{ old('horario_trabalho', $professor['horario_trabalho'] ?? '') }}" required>
        @error('horario_trabalho')<p class="erro-campo">{{ $message }}</p>@enderror

        <label for="especialidades">Especialidades <small>(uma por linha)</small></label>
        <textarea name="especialidades" id="especialidades" rows="3" required>{{ $especialidades }}</textarea>
        @error('especialidades')<p class="erro-campo">{{ $message }}</p>@enderror
        @error('especialidades.*')<p class="erro-campo">{{ $message }}</p>@enderror

        <label for="biografia">Biografia</label>
        <textarea name="biografia" id="biografia" rows="5" maxlength="1000" required>{{ old('biografia', $professor['biografia'] ?? '') }}</textarea>
        @error('biografia')<p class="erro-campo">{{ $message }}</p>@enderror

        <label class="check">
            <input type="checkbox" name="ativo" value="1" @checked(old('ativo', $professor['ativo'] ?? true))>
            Professor ativo (aparece no site e no agendamento)
        </label>

        <button class="btn cheio" type="submit">{{ $editando ? 'Salvar alterações' : 'Cadastrar professor' }}</button>
    </form>
</section>

<script>
const campoFoto = document.getElementById('foto');
const previaFoto = document.getElementById('previa-foto');
const fotoOriginal = previaFoto.src;
let urlTemporaria = null;

campoFoto.addEventListener('change', () => {
    if (urlTemporaria) {
        URL.revokeObjectURL(urlTemporaria);
        urlTemporaria = null;
    }
    const arquivo = campoFoto.files[0];
    if (arquivo) {
        urlTemporaria = URL.createObjectURL(arquivo);
        previaFoto.src = urlTemporaria;
    } else {
        previaFoto.src = fotoOriginal;
    }
});

const campoCpf = document.getElementById('cpf');
campoCpf.addEventListener('input', () => {
    const d = campoCpf.value.replace(/\D/g, '').slice(0, 11);
    campoCpf.value = d
        .replace(/^(\d{3})(\d)/, '$1.$2')
        .replace(/^(\d{3})\.(\d{3})(\d)/, '$1.$2.$3')
        .replace(/\.(\d{3})(\d)/, '.$1-$2');
});
</script>
@endsection
