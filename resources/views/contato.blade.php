<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enviar Mensagem</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .card {
            background: #fff;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, .1);
            width: 100%;
            max-width: 420px;
        }
        h1 { text-align: center; margin-top: 0; font-size: 1.5rem; }
        label { display: block; margin: 1rem 0 .3rem; font-weight: bold; }
        input[type="text"],
        input[type="email"],
        textarea {
            width: 100%;
            padding: .6rem;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 1rem;
            font-family: inherit;
        }
        textarea { resize: vertical; min-height: 120px; }
        button {
            width: 100%;
            margin-top: 1.5rem;
            padding: .8rem;
            background: #2563eb;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            cursor: pointer;
        }
        button:hover { background: #1d4ed8; }
        .alert { margin-top: 1rem; padding: .8rem; border-radius: 6px; }
        .alert-error { background: #fee2e2; color: #991b1b; }
        .alert-error ul { margin: 0; padding-left: 1.2rem; }
        .alert-success { background: #dcfce7; color: #166534; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Enviar Mensagem</h1>

        @if ($errors->any())
            <div class="alert alert-error">
                <ul>
                    @foreach ($errors->all() as $erro)
                        <li>{{ $erro }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('sucesso'))
            <div class="alert alert-success">{{ session('sucesso') }}</div>
        @endif

        <form action="{{ route('contato.enviar') }}" method="POST">
            @csrf

            <label for="nome">Nome completo</label>
            <input type="text" name="nome" id="nome"
                   value="{{ old('nome') }}" placeholder="Seu nome completo" required>

            <label for="email">E-mail</label>
            <input type="email" name="email" id="email"
                   value="{{ old('email') }}" placeholder="seu@email.com" required>

            <label for="assunto">Assunto</label>
            <input type="text" name="assunto" id="assunto"
                   value="{{ old('assunto') }}" placeholder="Assunto da mensagem" required>

            <label for="mensagem">Mensagem</label>
            <textarea name="mensagem" id="mensagem"
                      placeholder="Escreva sua mensagem" required>{{ old('mensagem') }}</textarea>

            <button type="submit">Enviar</button>
        </form>
    </div>
</body>
</html>
