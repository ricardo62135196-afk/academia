<?php

namespace App\Providers;

use App\Support\ProfessorRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            ProfessorRepository::class,
            fn (): ProfessorRepository => new ProfessorRepository(config('academia.professores_arquivo')),
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
