(function () {
  const R = document.documentElement;
  const ler = (k, d) => { try { const v = localStorage.getItem(k); return v === null ? d : JSON.parse(v); } catch (e) { return d; } };
  const gravar = (k, v) => { try { localStorage.setItem(k, JSON.stringify(v)); } catch (e) {} };

  // Opções de sistema aplicadas em todas as páginas
  const padrao = { tema: 'escuro', fonte: 16, contraste: false, reduzir: false };
  const aplicar = o => {
    R.dataset.theme = o.tema;
    o.contraste ? R.dataset.contraste = 'alto' : delete R.dataset.contraste;
    o.reduzir ? R.dataset.movimento = 'reduzido' : delete R.dataset.movimento;
    R.style.setProperty('--fs', o.fonte + 'px');
  };
  let opc = { ...padrao, ...ler('as_opcoes', {}) };
  aplicar(opc);

  // Pontos de integração com o back-end: troque por fetch() quando ele existir
  const carregarNotas = () => ler('as_notas', []);
  const salvarNotas = n => gravar('as_notas', n);

  const $ = s => document.querySelector(s);
  const aviso = t => {
    const a = document.createElement('div');
    a.className = 'aviso'; a.setAttribute('role', 'status'); a.textContent = t;
    document.body.append(a); setTimeout(() => a.remove(), 2600);
  };
  const dias = d => Math.round((new Date(d + 'T00:00:00') - new Date(new Date().toDateString())) / 864e5);
  const fmt = d => d.split('-').reverse().join('/');

  document.addEventListener('DOMContentLoaded', () => {
    // Menu lateral expansivo
    const shell = $('#shell');
    if (shell) {
      const btn = $('#toggleMenu');
      const f = ler('as_menu_fechado', innerWidth < 700);
      shell.classList.toggle('fechado', f);
      btn.setAttribute('aria-expanded', !f);
      btn.addEventListener('click', () => {
        const x = shell.classList.toggle('fechado');
        btn.setAttribute('aria-expanded', !x);
        gravar('as_menu_fechado', x);
      });
    }

    // Login continua simulado; o cadastro usa o endpoint do servidor.
    document.querySelectorAll('form[data-next]').forEach(f =>
      f.addEventListener('submit', e => { e.preventDefault(); location.href = f.dataset.next; }));

    const abas = document.querySelectorAll('[data-aba]');
    if (abas.length) {
      const p = new URLSearchParams(location.search);
      const mostrar = n => {
        abas.forEach(b => b.setAttribute('aria-selected', b.dataset.aba === n));
        document.querySelectorAll('[data-painel]').forEach(s => s.hidden = s.dataset.painel !== n);
      };
      abas.forEach(b => b.addEventListener('click', () => mostrar(b.dataset.aba)));
      mostrar(p.get('aba') === 'cadastro' ? 'cadastro' : 'login');
      if (['1', '2', '3'].includes(p.get('plano'))) {
        const d = $('#planoEscolhido'); d.textContent = 'Plano escolhido: Sete ' + p.get('plano'); d.hidden = false;
      }
    }

    // Opções
    if ($('#tema')) {
      const t = $('#tema'), f = $('#fonte'), c = $('#contraste'), r = $('#reduzir');
      t.value = opc.tema; f.value = opc.fonte; c.checked = opc.contraste; r.checked = opc.reduzir;
      const salvar = () => { opc = { tema: t.value, fonte: +f.value, contraste: c.checked, reduzir: r.checked }; aplicar(opc); gravar('as_opcoes', opc); };
      [t, f, c, r].forEach(x => x.addEventListener('input', salvar));
      $('#restaurar').addEventListener('click', () => {
        opc = { ...padrao }; aplicar(opc); gravar('as_opcoes', opc);
        t.value = opc.tema; f.value = opc.fonte; c.checked = r.checked = false; aviso('Opções restauradas');
      });
    }

    // Minha Conta
    const conta = $('form[data-simulacao]');
    if (conta) conta.addEventListener('submit', e => { e.preventDefault(); aviso('Alterações salvas (simulação)'); });

    // Bloco de notas com dia limite
    const lista = $('#listaNotas');
    if (lista) {
      const desenhar = () => {
        const n = carregarNotas().sort((a, b) => a.prazo.localeCompare(b.prazo));
        lista.innerHTML = '';
        if (!n.length) { lista.innerHTML = '<li class="vazio">Nenhuma anotação. Escreva a primeira acima.</li>'; return; }
        n.forEach(x => {
          const li = document.createElement('li');
          if (dias(x.prazo) < 0) li.className = 'vencida';
          const s = document.createElement('span'); s.textContent = x.texto;
          const sm = document.createElement('small'); sm.textContent = 'Até ' + fmt(x.prazo);
          const b = document.createElement('button'); b.className = 'btn'; b.type = 'button'; b.textContent = 'Excluir';
          b.addEventListener('click', () => { salvarNotas(carregarNotas().filter(y => y.id !== x.id)); desenhar(); });
          li.append(s, sm, b); lista.append(li);
        });
      };
      $('#formNota').addEventListener('submit', e => {
        e.preventDefault();
        const n = carregarNotas();
        n.push({ id: Date.now(), texto: $('#notaTexto').value.trim(), prazo: $('#notaPrazo').value });
        salvarNotas(n); e.target.reset(); desenhar();
      });
      desenhar();
    }

    // Timer do treino
    const tela = $('#tempo');
    if (tela) {
      let seg = 0, ini = 0, id = null;
      const mostrar = () => {
        tela.textContent = [Math.floor(seg / 3600), Math.floor(seg % 3600 / 60), seg % 60].map(v => String(v).padStart(2, '0')).join(':');
      };
      $('#iniciar').addEventListener('click', () => {
        if (id) return;
        ini = Date.now() - seg * 1000;
        id = setInterval(() => { seg = Math.floor((Date.now() - ini) / 1000); mostrar(); }, 250);
      });
      $('#pausar').addEventListener('click', () => { clearInterval(id); id = null; });
      $('#zerar').addEventListener('click', () => { clearInterval(id); id = null; seg = 0; mostrar(); });
    }

    // Calculadora de IMC
    const imc = $('#formImc');
    if (imc) imc.addEventListener('submit', e => {
      e.preventDefault();
      const a = +$('#altura').value / 100, v = +$('#peso').value / (a * a);
      const c = v < 18.5 ? 'abaixo do peso' : v < 25 ? 'peso adequado' : v < 30 ? 'sobrepeso' : 'obesidade';
      $('#resultadoImc').textContent = 'Seu IMC é ' + v.toFixed(1).replace('.', ',') + ' (' + c + '). É só uma referência.';
    });

    // Atendimento: chat (respostas simuladas)
    const chat = $('#formChat');
    if (chat) {
      const caixa = $('#chat');
      const add = (t, c) => { const m = document.createElement('p'); m.className = 'msg ' + c; m.textContent = t; caixa.append(m); caixa.scrollTop = caixa.scrollHeight; };
      add('Olá! Como podemos ajudar?', 'ac');
      chat.addEventListener('submit', e => {
        e.preventDefault();
        const i = $('#msg'), t = i.value.trim();
        if (!t) return;
        add(t, 'eu'); i.value = '';
        setTimeout(() => add('Recebemos sua mensagem. Respondemos em até 1 dia útil.', 'ac'), 700);
      });
    }

    // Atendimento: geração de pagamento (simulada)
    const pg = $('#formPagamento');
    if (pg) {
      const q = new URLSearchParams(location.search).get('plano');
      if (['1', '2', '3'].includes(q)) $('#planoPg').value = q;
      pg.addEventListener('submit', e => {
        e.preventDefault();
        const venc = new Date(); venc.setDate(venc.getDate() + 5);
        const p = document.createElement('p');
        p.textContent = $('#planoPg').selectedOptions[0].text + ' via ' + $('#metodo').value + '. Vence em ' + venc.toLocaleDateString('pt-BR') + '.';
        const c = document.createElement('code');
        c.textContent = Array.from({ length: 32 }, () => Math.floor(Math.random() * 10)).join('');
        $('#resultadoPg').replaceChildren(p, c);
      });
    }


    // Mural de fotos (por enquanto só neste navegador)
    const mural = $('#mural');
    if (mural) {
      const carregarFotos = () => ler('as_fotos', []); // troque por fetch() no back-end
      const salvarFotos = f => { try { localStorage.setItem('as_fotos', JSON.stringify(f)); return true; } catch (e) { return false; } };
      const reduzir = arq => new Promise((ok, erro) => {
        const img = new Image(), url = URL.createObjectURL(arq);
        img.onload = () => {
          const e = Math.min(1, 800 / Math.max(img.width, img.height));
          const c = document.createElement('canvas');
          c.width = Math.round(img.width * e); c.height = Math.round(img.height * e);
          c.getContext('2d').drawImage(img, 0, 0, c.width, c.height);
          URL.revokeObjectURL(url); ok(c.toDataURL('image/jpeg', 0.8));
        };
        img.onerror = () => { URL.revokeObjectURL(url); erro(); };
        img.src = url;
      });
      const desenhar = () => {
        const f = carregarFotos();
        mural.replaceChildren();
        if (!f.length) {
          const p = document.createElement('p'); p.className = 'vazio';
          p.textContent = 'Nenhuma foto ainda. Publique a primeira acima.'; mural.append(p); return;
        }
        f.forEach(x => {
          const fig = document.createElement('figure'), im = document.createElement('img'), cap = document.createElement('figcaption');
          const t = document.createElement('span'), d = document.createElement('small'), b = document.createElement('button');
          im.src = x.src; im.alt = x.legenda || 'Foto do mural';
          t.textContent = x.legenda || 'Sem legenda'; d.textContent = x.data;
          b.className = 'btn'; b.type = 'button'; b.textContent = 'Excluir foto';
          b.addEventListener('click', () => { salvarFotos(carregarFotos().filter(y => y.id !== x.id)); desenhar(); });
          cap.append(t, d, b); fig.append(im, cap); mural.append(fig);
        });
      };
      $('#formFoto').addEventListener('submit', async e => {
        e.preventDefault();
        const arq = $('#arquivo').files[0];
        if (!arq) return;
        if (!arq.type.startsWith('image/')) return aviso('Escolha um arquivo de imagem.');
        if (arq.size > 8 * 1024 * 1024) return aviso('A foto passa de 8 MB. Escolha uma menor.');
        try {
          const src = await reduzir(arq);
          const f = carregarFotos();
          f.unshift({ id: Date.now(), src, legenda: $('#legenda').value.trim(), data: new Date().toLocaleDateString('pt-BR') });
          if (!salvarFotos(f)) return aviso('Sem espaço no navegador. Exclua alguma foto e tente de novo.');
          e.target.reset(); desenhar(); aviso('Foto publicada');
        } catch (err) { aviso('Não foi possível ler essa imagem.'); }
      });
      desenhar();
    }

    // Notificações: datas do bloco de notas
    const nots = $('#notificacoes');
    if (nots) carregarNotas().forEach(n => {
      const d = dias(n.prazo);
      if (d > 3) return;
      const li = document.createElement('li'), s = document.createElement('span');
      s.textContent = 'Bloco de notas: ' + (d < 0 ? 'prazo vencido em ' + fmt(n.prazo) : d === 0 ? 'vence hoje' : 'vence em ' + d + ' dia(s)') + ' - ' + n.texto;
      li.append(s); nots.append(li);
    });
  });
})();
