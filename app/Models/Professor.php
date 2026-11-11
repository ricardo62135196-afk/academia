<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Professor extends Model
{
    protected $table = 'professores';

    protected $fillable = [
        'nome',
        'profissao',
        'email',
        'telefone',
        'cpf',
        'modalidade',
        'especialidades',
        'biografia',
        'horario_trabalho',
        'foto',
        'ativo',
    ];

    protected function casts(): array
    {
        return [
            'especialidades' => 'array',
            'ativo' => 'boolean',
        ];
    }

    protected function fotoUrl(): Attribute
    {
        return Attribute::get(function (): string {
            $foto = $this->foto ?: config('academia.foto_padrao');

            if (str_starts_with($foto, 'img/')) {
                return asset($foto);
            }

            return asset('storage/'.$foto);
        });
    }
}
