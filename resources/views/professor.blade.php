@extends('layouts.publico')
@section('titulo', 'Professor')
@section('pagina')
<a class="voltar" href="{{ url('/professores') }}">&larr; Todos os professores</a>

<article id="perfil-professor" class="perfil" aria-live="polite">
    <p>Carregando professor...</p>
</article>

<script>
document.addEventListener('DOMContentLoaded', async () => {
    const perfil = document.getElementById('perfil-professor');

    try {
        const resposta = await fetch('{{ url('/api/professores') }}/{{ (int) $id }}', { headers: { 'Accept': 'application/json' } });
        if (resposta.status === 404) {
            perfil.textContent = 'Professor não encontrado.';
            return;
        }
        if (!resposta.ok) throw new Error('Erro ' + resposta.status);
        const p = await resposta.json();

        document.title = p.nome + ' | Academia Sete';
        perfil.innerHTML = '';

        const foto = document.createElement('img');
        foto.className = 'perfil-foto';
        foto.src = p.foto;
        foto.alt = 'Foto de ' + p.nome;
        foto.width = 400;
        foto.height = 400;

        const info = document.createElement('div');
        info.className = 'perfil-info';

        const etiqueta = document.createElement('span');
        etiqueta.className = 'etiqueta';
        etiqueta.textContent = p.modalidade;

        const nome = document.createElement('h1');
        nome.textContent = p.nome;

        if (p.profissao) {
            const profissao = document.createElement('p');
            profissao.className = 'destaque';
            profissao.textContent = p.profissao;
            info.append(etiqueta, nome, profissao);
        } else {
            info.append(etiqueta, nome);
        }

        const tituloEsp = document.createElement('h2');
        tituloEsp.className = 'perfil-sub';
        tituloEsp.textContent = 'Especialidades';
        const chips = document.createElement('ul');
        chips.className = 'chips';
        p.especialidades.forEach(e => {
            const li = document.createElement('li');
            li.textContent = e;
            chips.appendChild(li);
        });

        const tituloBio = document.createElement('h2');
        tituloBio.className = 'perfil-sub';
        tituloBio.textContent = 'Biografia';
        const bio = document.createElement('p');
        bio.textContent = p.biografia;

        const tituloHor = document.createElement('h2');
        tituloHor.className = 'perfil-sub';
        tituloHor.textContent = 'Horário de trabalho';
        const horario = document.createElement('p');
        horario.className = 'destaque';
        horario.textContent = p.horario_trabalho;

        const acoes = document.createElement('div');
        acoes.className = 'perfil-acoes';
        const agendar = document.createElement('a');
        agendar.className = 'btn cheio';
        agendar.href = '{{ url('/agendamento') }}';
        agendar.textContent = 'Agendar uma aula';
        acoes.appendChild(agendar);

        info.append(tituloEsp, chips, tituloBio, bio, tituloHor, horario, acoes);
        perfil.append(foto, info);
    } catch (erro) {
        perfil.textContent = 'Não foi possível carregar o professor.';
        console.error(erro);
    }
});
</script>
@endsection
