<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class Cpf implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $digitos = preg_replace('/\D+/', '', (string) $value);

        if (! $this->temDigitosVerificadoresValidos($digitos)) {
            $fail('O CPF informado não é válido.');
        }
    }

    private function temDigitosVerificadoresValidos(string $digitos): bool
    {
        if (strlen($digitos) !== 11 || preg_match('/^(\d)\1{10}$/', $digitos)) {
            return false;
        }

        foreach ([9, 10] as $posicao) {
            $soma = 0;

            for ($i = 0; $i < $posicao; $i++) {
                $soma += (int) $digitos[$i] * ($posicao + 1 - $i);
            }

            $esperado = ($soma * 10 % 11) % 10;

            if ((int) $digitos[$posicao] !== $esperado) {
                return false;
            }
        }

        return true;
    }
}
