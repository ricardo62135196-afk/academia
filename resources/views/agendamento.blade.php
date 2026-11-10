@extends('layouts.painel')
@section('titulo', 'Agendar Aula')
@section('pagina')
<p>Escolha a modalidade, o dia e o horário. Enviamos o pedido pelo WhatsApp da academia.</p>

<section class="form-card" id="agendamento">
    <form id="form-agendamento">
        <label for="nome">Nome</label>
        <input type="text" name="nome" id="nome"
               value="{{ old('nome') }}" placeholder="Seu nome completo" required>

        <fieldset class="opcoes">
            <legend>Modalidade</legend>
            <label>
                <input type="radio" name="modalidade" value="Musculação"
                       {{ old('modalidade') === 'Musculação' ? 'checked' : '' }} required>
                Musculação
            </label>
            <label>
                <input type="radio" name="modalidade" value="Pilates"
                       {{ old('modalidade') === 'Pilates' ? 'checked' : '' }}>
                Pilates
            </label>
            <label>
                <input type="radio" name="modalidade" value="Funcional"
                       {{ old('modalidade') === 'Funcional' ? 'checked' : '' }}>
                Funcional
            </label>
            <label>
                <input type="radio" name="modalidade" value="Spinning"
                       {{ old('modalidade') === 'Spinning' ? 'checked' : '' }}>
                Spinning
            </label>
        </fieldset>

        <fieldset class="opcoes professores" id="escolha-professor" aria-live="polite">
            <legend>Professor</legend>
            <p class="dica-professor">Escolha uma modalidade para ver os professores.</p>
        </fieldset>

        <div class="duas-colunas">
            <div>
                <label for="data">Data</label>
                <input type="date" name="data" id="data"
                       value="{{ old('data') }}" min="{{ date('Y-m-d') }}" required>
            </div>
            <div>
                <label for="horario">Horário</label>
                <input type="time" name="horario" id="horario"
                       value="{{ old('horario') }}" required>
            </div>
        </div>

        <button class="btn cheio" type="submit">Enviar pelo WhatsApp</button>
    </form>
</section>

<script>
    // Troque pelo número da academia: 55 + DDD + número, só dígitos
    const WHATSAPP_NUMERO = '5500000000000';

    const form = document.getElementById('form-agendamento');
    const caixaProfessor = document.getElementById('escolha-professor');
    let professores = [];
    let falhouCarregar = false;

    function mostrarAviso(texto) {
        caixaProfessor.querySelectorAll('label, .dica-professor').forEach(el => el.remove());
        const aviso = document.createElement('p');
        aviso.className = 'dica-professor';
        aviso.textContent = texto;
        caixaProfessor.appendChild(aviso);
    }

    function mostrarProfessores(modalidade) {
        if (falhouCarregar) {
            mostrarAviso('Não foi possível carregar os professores.');
            return;
        }

        const doGrupo = professores.filter(p => p.modalidade === modalidade);
        if (doGrupo.length === 0) {
            mostrarAviso('Nenhum professor disponível para esta modalidade.');
            return;
        }

        caixaProfessor.querySelectorAll('label, .dica-professor').forEach(el => el.remove());
        doGrupo.forEach((p, i) => {
            const label = document.createElement('label');
            const radio = document.createElement('input');
            radio.type = 'radio';
            radio.name = 'professor';
            radio.value = p.nome;
            radio.required = true;
            if (i === 0 && doGrupo.length === 1) radio.checked = true;

            const texto = document.createElement('span');
            texto.className = 'opcao-texto';
            const nome = document.createElement('strong');
            nome.textContent = p.nome;
            const horario = document.createElement('small');
            horario.textContent = p.horario_trabalho;
            texto.append(nome, horario);

            label.append(radio, texto);
            caixaProfessor.appendChild(label);
        });
    }

    form.querySelectorAll('input[name="modalidade"]').forEach(r => {
        r.addEventListener('change', () => mostrarProfessores(r.value));
    });

    fetch('{{ url('/api/professores') }}', { headers: { 'Accept': 'application/json' } })
        .then(resposta => {
            if (!resposta.ok) throw new Error('Erro ' + resposta.status);
            return resposta.json();
        })
        .then(dados => { professores = dados; })
        .catch(erro => { falhouCarregar = true; console.error(erro); })
        .finally(() => {
            const marcada = form.querySelector('input[name="modalidade"]:checked');
            if (marcada) mostrarProfessores(marcada.value);
        });

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        if (!this.reportValidity()) return;

        const nome = document.getElementById('nome').value.trim();
        const modalidade = document.querySelector('input[name="modalidade"]:checked').value;
        const escolhido = document.querySelector('input[name="professor"]:checked');
        const professor = escolhido ? escolhido.value : null;
        const [ano, mes, dia] = document.getElementById('data').value.split('-');
        const horario = document.getElementById('horario').value;

        const mensagem =
            `Olá! Gostaria de agendar uma aula.\n\n` +
            `Nome: ${nome}\n` +
            `Modalidade: ${modalidade}\n` +
            (professor ? `Professor: ${professor}\n` : '') +
            `Data: ${dia}/${mes}/${ano}\n` +
            `Horário: ${horario}`;

        const url = `https://wa.me/${WHATSAPP_NUMERO}?text=${encodeURIComponent(mensagem)}`;
        window.open(url, '_blank');
    });
</script>
@endsection
