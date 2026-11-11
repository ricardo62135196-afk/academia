@extends('layouts.painel')
@section('titulo', 'Gerenciar Professores')
@section('pagina')
<p>Cadastre, edite ou exclua os professores que aparecem no site e no agendamento.</p>

@if (session('sucesso'))
    <div class="alerta sucesso" role="status">{{ session('sucesso') }}</div>
@endif

<div class="admin-topo">
    <a class="btn cheio" href="{{ route('admin.professores.create') }}">Novo professor</a>
</div>

@if (count($professores) === 0)
    <p class="dica">Nenhum professor cadastrado ainda.</p>
@else
    <div class="tabela-rolagem">
        <table class="tabela-admin">
            <thead>
                <tr>
                    <th scope="col">Foto</th>
                    <th scope="col">Nome</th>
                    <th scope="col">Profissão</th>
                    <th scope="col">Modalidade</th>
                    <th scope="col">Horário de trabalho</th>
                    <th scope="col">Status</th>
                    <th scope="col">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($professores as $professor)
                    <tr>
                        <td><img class="miniatura" src="{{ $professor->foto_url }}" alt="Foto de {{ $professor['nome'] }}" width="56" height="56" loading="lazy"></td>
                        <td><strong>{{ $professor['nome'] }}</strong></td>
                        <td>{{ $professor['profissao'] ?: '-' }}</td>
                        <td>{{ $professor['modalidade'] }}</td>
                        <td>{{ $professor['horario_trabalho'] }}</td>
                        <td>
                            <span class="status {{ $professor['ativo'] ? 'ativo' : 'inativo' }}">
                                {{ $professor['ativo'] ? 'Ativo' : 'Inativo' }}
                            </span>
                        </td>
                        <td>
                            <div class="acoes-linha">
                                @if ($professor['ativo'])
                                    <a class="btn ciano" href="{{ url('/professores/' . $professor['id']) }}">Ver perfil</a>
                                @endif
                                <a class="btn ciano" href="{{ route('admin.professores.edit', $professor['id']) }}">Editar</a>
                                <form class="form-excluir" method="POST" action="{{ route('admin.professores.destroy', $professor['id']) }}" data-nome="{{ $professor['nome'] }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn perigo" type="submit">Excluir</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif

<script>
document.querySelectorAll('.form-excluir').forEach(form => {
    form.addEventListener('submit', evento => {
        if (!confirm('Excluir o professor ' + form.dataset.nome + '? Esta ação não pode ser desfeita.')) {
            evento.preventDefault();
        }
    });
});
</script>
@endsection
