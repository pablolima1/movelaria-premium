<?php

namespace App\Providers;

use App\Enums\StatusProjeto;
use App\Models\Projeto;
use App\Services\AliquotaResolver;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Um cache de alíquotas por request, compartilhado entre os services.
        $this->app->scoped(AliquotaResolver::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Projetos disponíveis no "+ Lançar" da barra inferior (mobile).
        View::composer('layouts.partials.lancar-modal', function ($view) {
            $view->with('projetosLancamento', auth()->check()
                ? Projeto::query()
                    ->where('status', '!=', StatusProjeto::Cancelado->value)
                    ->with('cliente:id,nome')
                    ->orderByDesc('updated_at')
                    ->limit(100)
                    ->get(['id', 'nome', 'cliente_id'])
                    ->map(fn (Projeto $p) => ['id' => $p->id, 'nome' => $p->nome . ($p->cliente ? ' — ' . $p->cliente->nome : '')])
                    ->all()
                : []);
        });
    }
}
