@extends('layouts.app')

@use('App\Helpers\Formatter')

@php
    $abas = [
        'abertas' => ['Em aberto', $resumo['em_aberto_qtd']],
        'atrasadas' => ['Atrasadas', $resumo['atrasadas_qtd']],
        'pagas' => ['Pagas', null],
    ];
    $inputClass = 'dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30';
@endphp

@section('content')
    <x-common.page-breadcrumb pageTitle="Cobranças" />

    <div class="space-y-6">
        @include('pages.projetos.partials.flash')

        {{-- Resumo --}}
        <div class="grid grid-cols-2 gap-3 md:gap-4 lg:grid-cols-3">
            <a href="{{ route('cobrancas.index', ['aba' => 'atrasadas']) }}"
                class="col-span-2 rounded-2xl border p-4 transition sm:p-5 lg:col-span-1 {{ $resumo['atrasadas_qtd'] > 0 ? 'border-error-200 bg-error-50 hover:border-error-300 dark:border-error-500/30 dark:bg-error-500/10' : 'border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]' }}">
                <p class="text-theme-xs sm:text-sm {{ $resumo['atrasadas_qtd'] > 0 ? 'text-error-700 dark:text-error-400' : 'text-gray-500 dark:text-gray-400' }}">Em atraso</p>
                <p class="mt-1 text-2xl font-bold {{ $resumo['atrasadas_qtd'] > 0 ? 'text-error-700 dark:text-error-400' : 'text-gray-800 dark:text-white/90' }}">{{ Formatter::money($resumo['atrasado']) }}</p>
                <p class="mt-0.5 text-theme-xs {{ $resumo['atrasadas_qtd'] > 0 ? 'text-error-600/80 dark:text-error-400/80' : 'text-gray-500 dark:text-gray-400' }}">
                    {{ $resumo['atrasadas_qtd'] }} {{ $resumo['atrasadas_qtd'] === 1 ? 'parcela' : 'parcelas' }}
                </p>
            </a>
            <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03] sm:p-5">
                <p class="text-theme-xs text-gray-500 sm:text-sm dark:text-gray-400">Próximos 30 dias</p>
                <p class="mt-1 text-base font-semibold text-gray-800 sm:text-xl dark:text-white/90">{{ Formatter::money($resumo['proximos']) }}</p>
                <p class="mt-0.5 text-theme-xs text-gray-500 dark:text-gray-400">{{ $resumo['proximos_qtd'] }} {{ $resumo['proximos_qtd'] === 1 ? 'parcela' : 'parcelas' }}</p>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03] sm:p-5">
                <p class="text-theme-xs text-gray-500 sm:text-sm dark:text-gray-400">Total a receber</p>
                <p class="mt-1 text-base font-semibold text-gray-800 sm:text-xl dark:text-white/90">{{ Formatter::money($resumo['em_aberto']) }}</p>
                <p class="mt-0.5 text-theme-xs text-gray-500 dark:text-gray-400">{{ $resumo['em_aberto_qtd'] }} {{ $resumo['em_aberto_qtd'] === 1 ? 'parcela' : 'parcelas' }}</p>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center gap-2 border-b border-gray-100 p-2 dark:border-gray-800">
                @foreach ($abas as $chave => [$titulo, $qtd])
                    <a href="{{ route('cobrancas.index', array_filter(['aba' => $chave, 'q' => $busca])) }}"
                        @class([
                            'flex flex-1 items-center justify-center gap-2 rounded-lg px-3 py-2.5 text-sm font-medium transition',
                            'bg-gray-100 text-gray-800 dark:bg-white/[0.06] dark:text-white/90' => $aba === $chave,
                            'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200' => $aba !== $chave,
                        ])>
                        {{ $titulo }}
                        @if (! is_null($qtd))
                            <span class="rounded-full bg-white px-2 py-0.5 text-theme-xs text-gray-600 shadow-theme-xs dark:bg-gray-800 dark:text-gray-300">{{ $qtd }}</span>
                        @endif
                    </a>
                @endforeach
            </div>

            <div class="p-4 sm:p-6">
                <form method="GET" action="{{ route('cobrancas.index') }}" class="flex gap-3">
                    <input type="hidden" name="aba" value="{{ $aba }}">
                    <input type="text" name="q" value="{{ $busca }}" placeholder="Buscar por projeto ou cliente" class="{{ $inputClass }}" />
                    <button type="submit" class="inline-flex h-11 shrink-0 items-center justify-center rounded-lg bg-brand-500 px-4 text-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600">Buscar</button>
                </form>

                @if ($parcelas->isEmpty())
                    <div class="py-12 text-center">
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300">
                            {{ match ($aba) { 'atrasadas' => 'Nenhuma parcela em atraso. 🎉', 'pagas' => 'Nenhuma parcela recebida ainda.', default => 'Nenhuma parcela em aberto.' } }}
                        </p>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">As parcelas são criadas ao cadastrar um projeto como "Parcelado".</p>
                    </div>
                @else
                    <ul class="mt-2 divide-y divide-gray-100 dark:divide-gray-800">
                        @foreach ($parcelas as $parcela)
                            @include('pages.cobrancas.partials.parcela-item', ['parcela' => $parcela, 'comProjeto' => true])
                        @endforeach
                    </ul>

                    @if ($parcelas->hasPages())
                        <div class="mt-4 border-t border-gray-100 pt-4 dark:border-gray-800">
                            {{ $parcelas->links() }}
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>

    @include('pages.cobrancas.partials.modals')
@endsection
