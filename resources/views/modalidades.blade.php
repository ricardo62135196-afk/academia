@extends('layouts.painel')
@section('titulo', 'Modalidades')
@section('pagina')
<section id="modalidades" class="pagina-modalidades">
    <p>Conheça as atividades disponíveis em nossa academia e veja professor e horários de cada uma.</p>

    <div class="modalidades-grid">
        <div id="lista-modalidades">
            <p>Carregando modalidades...</p>
        </div>

        <div class="modalidades-painel">
            <p id="dica-modalidade" class="dica">Selecione uma modalidade para ver os detalhes.</p>
            <div id="detalhes-modalidade" aria-live="polite"></div>
            <a class="btn ciano" id="agendar-modalidade" href="{{ url('/agendamento') }}" hidden>Agendar uma aula</a>
        </div>
    </div>
</section>
<script>
document.addEventListener('DOMContentLoaded', async () => {
    const lista = document.getElementById('lista-modalidades');
    const detalhes = document.getElementById('detalhes-modalidade');
    const dica = document.getElementById('dica-modalidade');
    const agendar = document.getElementById('agendar-modalidade');

    async function buscar(caminho) {
        const resposta = await fetch(caminho, { headers: { 'Accept': 'application/json' } });
        if (!resposta.ok) throw new Error('Erro ' + resposta.status + ' em ' + caminho);
        return resposta.json();
    }

    async function mostrarDetalhes(id) {
        dica.hidden = true;
        agendar.hidden = true;
        detalhes.textContent = 'Carregando detalhes...';
        try {
            const [modalidade, horarios] = await Promise.all([
                buscar('{{ url('/api/modalidades') }}/' + id),
                buscar('{{ url('/api/modalidades') }}/' + id + '/horarios'),
            ]);

            detalhes.innerHTML = '';

            const criar = (tag, texto, classe) => {
                const el = document.createElement(tag);
                if (texto !== undefined) el.textContent = texto;
                if (classe) el.className = classe;
                return el;
            };
            const listaItens = (itens, classe) => {
                const ul = criar('ul', undefined, classe);
                itens.forEach(t => ul.appendChild(criar('li', t)));
                return ul;
            };
            const ficha = [
                ['Nível', modalidade.nivel],
                ['Duração', modalidade.duracao],
                ['Intensidade', modalidade.intensidade],
                ['Público', modalidade.publico],
            ];
            const dl = criar('dl', undefined, 'ficha');
            ficha.forEach(([rotulo, valor]) => {
                const bloco = criar('div');
                bloco.append(criar('dt', rotulo), criar('dd', valor));
                dl.appendChild(bloco);
            });

            const professores = criar('ul', undefined, 'lista-professores');
            modalidade.professores.forEach(prof => {
                const li = criar('li');
                const link = criar('a', prof.nome);
                link.href = '{{ url('/professores') }}/' + prof.id;
                li.append(link, criar('small', prof.horario_trabalho));
                professores.appendChild(li);
            });

            detalhes.append(
                criar('h3', modalidade.nome),
                criar('p', modalidade.sobre),
                dl,
                criar('h4', 'Benefícios', 'sub'),
                listaItens(modalidade.beneficios, 'beneficios'),
                criar('h4', 'Horários das aulas', 'sub'),
                listaItens(horarios.horarios, 'horarios-aula'),
                criar('h4', 'Professores', 'sub'),
                professores,
                criar('h4', 'O que levar', 'sub'),
                listaItens(modalidade.levar, 'chips'),
                criar('h4', 'Incluída nos planos', 'sub'),
                listaItens(modalidade.planos, 'chips')
            );
            agendar.hidden = false;
        } catch (erro) {
            detalhes.textContent = 'Não foi possível carregar os detalhes.';
            console.error(erro);
        }
    }

    try {
        const modalidades = await buscar('{{ url('/api/modalidades') }}');
        lista.innerHTML = '';
        modalidades.forEach(m => {
            const botao = document.createElement('button');
            botao.type = 'button';
            botao.textContent = m.nome;
            botao.setAttribute('aria-pressed', 'false');
            botao.addEventListener('click', () => {
                lista.querySelectorAll('button').forEach(b => b.setAttribute('aria-pressed', 'false'));
                botao.setAttribute('aria-pressed', 'true');
                mostrarDetalhes(m.id);
            });
            lista.appendChild(botao);
        });
    } catch (erro) {
        lista.textContent = 'Não foi possível carregar as modalidades.';
        console.error(erro);
    }
});
</script>
@endsection
