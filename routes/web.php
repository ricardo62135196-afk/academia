<?php

use App\Http\Controllers\AcademiaController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AlunosController;
use App\Http\Controllers\ContatoController;
use App\Http\Controllers\AgendamentoController;
use App\Http\Controllers\ProfessorAdminController;
use App\Http\Controllers\ProfessorCadastroController;

Route::get('/alunos', [AlunosController::class, 'index'])->name('alunos.index');
Route::post('/alunos', [AlunosController::class, 'store'])->name('alunos.store');
Route::get('/professores/cadastrar', [ProfessorCadastroController::class, 'create'])->name('professores.create');
Route::post('/professores/cadastrar', [ProfessorCadastroController::class, 'store'])->name('professores.store');

Route::view('/', 'inicio');
Route::view('/login', 'auth');
Route::view('/principal', 'principal');
Route::view('/modalidades', 'modalidades');
Route::view('/professores', 'professores');
Route::view('/professores/{id}', 'professor')->whereNumber('id');
Route::view('/meu-objetivo', 'meu-objetivo');
Route::view('/dicas-de-treino', 'dicas-de-treino');
Route::view('/atendimento', 'atendimento');
Route::view('/opcoes', 'opcoes');
Route::view('/minha-conta', 'minha-conta');
Route::view('/notificacoes', 'notificacoes');
Route::view('/mural', 'mural');

Route::get('/api/modalidades', [AcademiaController::class, 'modalidades']);
Route::get('/api/modalidades/{id}', [AcademiaController::class, 'modalidade'])->whereNumber('id');
Route::get('/api/modalidades/{id}/horarios', [AcademiaController::class, 'horarios'])->whereNumber('id');
Route::get('/api/professores', [AcademiaController::class, 'professores']);
Route::get('/api/professores/{id}', [AcademiaController::class, 'professor'])->whereNumber('id');
Route::get('/api/planos', [AcademiaController::class, 'planos']);
Route::get('/api/planos/{id}', [AcademiaController::class, 'plano'])->whereNumber('id');

route::get('/imc', [App\Http\Controllers\ImcController::class, 'index'])->name('imc.index');
route::post('/imc', [App\Http\Controllers\ImcController::class, 'calcularImc'])->name('imc.calcularImc');

Route::get('/agendamento', [AgendamentoController::class, 'index'])->name('agendamento.index');
Route::post('/agendamento', [AgendamentoController::class, 'store'])->name('agendamento.store');

Route::view('/contato', 'contato')->name('contato');

Route::get('/contato', [ContatoController::class, 'index'])->name('contato.index');
Route::post('/contato', [ContatoController::class, 'enviar'])->name('contato.enviar');

Route::prefix('admin/professores')->name('admin.professores.')->controller(ProfessorAdminController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/novo', 'create')->name('create');
    Route::post('/', 'store')->name('store');
    Route::get('/{id}/editar', 'edit')->whereNumber('id')->name('edit');
    Route::put('/{id}', 'update')->whereNumber('id')->name('update');
    Route::delete('/{id}', 'destroy')->whereNumber('id')->name('destroy');
});
