<?php
 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AcademiaController;
 
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
 
Route::get('/planos', [AcademiaController::class, 'planos']);
Route::get('/planos/{id}', [AcademiaController::class, 'plano'])
    ->whereNumber('id');
 
Route::get('/modalidades', [AcademiaController::class, 'modalidades']);
Route::get('/modalidades/{id}', [AcademiaController::class, 'modalidade'])
    ->whereNumber('id');
Route::get('/modalidades/{id}/horarios',
    [AcademiaController::class, 'horarios']);