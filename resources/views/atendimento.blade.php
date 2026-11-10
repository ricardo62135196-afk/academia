@extends('layouts.painel')
@section('titulo', 'Atendimento')
@section('pagina')
<div class="duas">
  <section class="bloco">
    <h2>Fale com a gente</h2>
    <div id="chat" class="chat" aria-live="polite"></div>
    <form id="formChat" class="linha">
      <label>Mensagem<input id="msg" type="text" required maxlength="300"></label>
      <button class="btn cheio" type="submit">Enviar mensagem</button>
    </form>
  </section>
  <section class="bloco">
    <h2>Contatos</h2>
    <ul class="links">
      <li>Telefone: (47) 3333-0007</li>
      <li>WhatsApp: (47) 99999-0007</li>
      <li>E-mail: contato@@academiasete.com.br</li>
      <li>Instagram: @@academiasete</li>
      <li>Facebook: /academiasete</li>
    </ul>
  </section>
</div>
<section class="bloco" id="pagamento">
  <h2>Gerar pagamento</h2>
  <p>Simulação: nenhum valor é cobrado.</p>
  <form id="formPagamento" class="linha">
    <label>Plano
      <select id="planoPg"><option value="1">Sete 1 (R$ 59,90)</option><option value="2">Sete 2 (R$ 89,90)</option><option value="3">Sete 3 (R$ 119,90)</option></select>
    </label>
    <label>Forma de pagamento
      <select id="metodo"><option>Pix</option><option>Boleto</option></select>
    </label>
    <button class="btn cheio" type="submit">Gerar pagamento</button>
  </form>
  <div id="resultadoPg" class="destaque" role="status"></div>
</section>
@endsection
