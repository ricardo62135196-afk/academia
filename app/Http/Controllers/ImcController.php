<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ImcController extends Controller
{
    public function index(){
        return view('imc');
    }

    public function calcularImc(Request $request){
        $peso = $request->input('peso');
        $altura = $request->input('altura');

        if($altura <= 0){
            return redirect()->back()->with('error', 'La altura debe ser mayor que cero.');
        }

        $imc = $peso / ($altura * $altura);
        $imc = round($imc, 2);

        if($imc < 18.5){
            $classificacao = "Abaixo do peso";
        } elseif($imc < 25){
            $classificacao = "Peso normal";
        } elseif($imc < 30){
            $classificacao = "Sobrepeso";
        } else {
            $classificacao = "Obesidade";
        }

        return back()->withInput()->with('imc', $imc)->with('classificacao', $classificacao);
    }
}
