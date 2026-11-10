<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ContatoController extends Controller
{
    // E-mail que vai RECEBER as mensagens (troque pelo seu)
    private const DESTINO = 'seu-email@gmail.com';

    public function index()
    {
        return view('contato');
    }

    public function enviar(Request $request)
    {
        $dados = $request->validate([
            'nome'     => 'required|string|max:100',
            'email'    => 'required|email|max:150',
            'assunto'  => 'required|string|max:150',
            'mensagem' => 'required|string|max:5000',
        ]);

        $texto = "Nome: {$dados['nome']}\n"
               . "E-mail para resposta: {$dados['email']}\n\n"
               . $dados['mensagem'];

        try {
            Mail::raw($texto, function ($mail) use ($dados) {
                $mail->to(self::DESTINO)
                     ->replyTo($dados['email'], $dados['nome'])
                     ->subject($dados['assunto']);
            });
        } catch (\Throwable $e) {
            Log::error('Falha ao enviar e-mail de contato: ' . $e->getMessage());

            return back()
                ->withInput()
                ->withErrors(['email' => 'Não foi possível enviar a mensagem agora. Tente novamente mais tarde.']);
        }

        return back()->with('sucesso', 'Mensagem enviada com sucesso!');
    }
}
