@extends('layouts.publico')
@section('titulo', 'Professores')
@section('pagina')
<h1>Professores</h1>
<p>Conheça a equipe da Academia Sete: profissionais que acompanham de perto o seu treino.</p>

<div class="filtro-professores">
    <label for="filtro-modalidade">Filtrar por modalidade
        <select id="filtro-modalidade">
            <option value="">Todas</option>
        </select>
    </label>
</div>

<div id="lista-professores" aria-live="polite">
    <p>Carregando professores...</p>
</div>

<script>
document.addEventListener('DOMContentLoaded', async () => {
    const lista = document.getElementById('lista-professores');
    const filtro = document.getElementById('filtro-modalidade');
    let professores = [];

    function desenhar() {
        const escolhida = filtro.value;
        const visiveis = professores.filter(p => !escolhida || p.modalidade === escolhida);
        lista.innerHTML = '';

        if (visiveis.length === 0) {
            lista.textContent = 'Nenhum professor encontrado.';
            return;
        }

        visiveis.forEach(p => {
            const card = document.createElement('article');
            card.className = 'professor';

            const link = document.createElement('a');
            link.href = '{{ url('/professores') }}/' + p.id;
            link.className = 'professor-link';
            link.setAttribute('aria-label', 'Ver perfil de ' + p.nome);

            const foto = document.createElement('img');
            foto.src = p.foto;
            foto.alt = 'Foto de ' + p.nome;
            foto.loading = 'lazy';
            foto.width = 400;
            foto.height = 400;
            link.appendChild(foto);

            const corpo = document.createElement('div');
            corpo.className = 'professor-corpo';

            const etiqueta = document.createElement('span');
            etiqueta.className = 'etiqueta';
            etiqueta.textContent = p.modalidade;

            const nome = document.createElement('h2');
            nome.textContent = p.nome;

            const chips = document.createElement('ul');
            chips.className = 'chips';
            p.especialidades.forEach(e => {
                const li = document.createElement('li');
                li.textContent = e;
                chips.appendChild(li);
            });

            const bio = document.createElement('p');
            bio.className = 'bio-resumo';
            bio.textContent = p.biografia;

            const ver = document.createElement('a');
            ver.className = 'btn ciano';
            ver.href = link.href;
            ver.textContent = 'Ver perfil';

            corpo.append(etiqueta, nome, chips, bio, ver);
            card.append(link, corpo);
            lista.appendChild(card);
        });
    }

    try {
        const resposta = await fetch('{{ url('/api/professores') }}', { headers: { 'Accept': 'application/json' } });
        if (!resposta.ok) throw new Error('Erro ' + resposta.status);
        professores = await resposta.json();

        [...new Set(professores.map(p => p.modalidade))].forEach(m => {
            const opcao = document.createElement('option');
            opcao.value = m;
            opcao.textContent = m;
            filtro.appendChild(opcao);
        });

        filtro.addEventListener('change', desenhar);
        desenhar();
    } catch (erro) {
        lista.textContent = 'Não foi possível carregar os professores.';
        console.error(erro);
    }
});
</script>
@endsection
