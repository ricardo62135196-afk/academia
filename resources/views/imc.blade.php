@extends('layouts.painel')
@section('titulo', 'Calculadora de IMC')
@section('pagina')
<p>Informe seu peso e sua altura para calcular o índice de massa corporal.</p>

<section class="form-card" id="imc">
    <form action="{{ route('imc.calcularImc') }}" method="POST">
        @csrf

        <div class="duas-colunas">
            <div>
                <label for="peso">Peso (kg)</label>
                <input type="number" step="0.1" min="1" name="peso" id="peso"
                       value="{{ old('peso') }}" placeholder="Ex.: 70.5" required>
            </div>
            <div>
                <label for="altura">Altura (m)</label>
                <input type="number" step="0.01" min="0.1" name="altura" id="altura"
                       value="{{ old('altura') }}" placeholder="Ex.: 1.75" required>
            </div>
        </div>

        <button class="btn cheio" type="submit">Calcular</button>
    </form>

    @if (session('error'))
        <div class="alerta erro" role="alert">{{ session('error') }}</div>
    @endif

    @if (session('imc'))
        <div class="alerta resultado-imc" role="status">
            <span>Seu IMC é</span>
            <strong class="valor">{{ session('imc') }}</strong>
            <span class="classificacao">{{ session('classificacao') }}</span>
        </div>
    @endif
</section>
@endsection
