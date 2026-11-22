@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Home" />

    <div class="space-y-6">
        <section class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900 sm:p-8">
            <p class="text-sm font-medium text-brand-500">Seu novo projeto</p>
            <h1 class="mt-2 text-2xl font-semibold text-gray-800 dark:text-white/90">
                Bem-vindo, {{ auth()->user()->name }}!
            </h1>
            <p class="mt-3 max-w-2xl text-sm text-gray-500 dark:text-gray-400">
                Esta é a tela inicial de exemplo. A estrutura do Laravel, o layout TailAdmin e a autenticação Fortify
                estão prontos para você começar a desenvolver.
            </p>
        </section>

        <section class="grid grid-cols-1 gap-4 md:grid-cols-3 md:gap-6" aria-label="Recursos do projeto">
            <article class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
                <h2 class="text-base font-semibold text-gray-800 dark:text-white/90">Laravel</h2>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    Rotas, controllers, models e migrations organizados para evoluir com o projeto.
                </p>
            </article>

            <article class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
                <h2 class="text-base font-semibold text-gray-800 dark:text-white/90">TailAdmin</h2>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    Layout responsivo com navegação lateral, tema claro/escuro e componentes reutilizáveis.
                </p>
            </article>

            <article class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
                <h2 class="text-base font-semibold text-gray-800 dark:text-white/90">Fortify</h2>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    Login e cadastro de usuários integrados às telas do projeto.
                </p>
            </article>
        </section>
    </div>
@endsection
