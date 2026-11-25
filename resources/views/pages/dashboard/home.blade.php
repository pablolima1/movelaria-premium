@extends('layouts.app')

@use('App\Helpers\Formatter')

@php
    $mesLabel = ucfirst($mes->locale('pt_BR')->translatedFormat('F'));
@endphp

@section('content')
    <div class="space-y-6">
        @include('pages.dashboard.partials.month-nav')

        {{-- Resumo do ano --}}
        <section aria-labelledby="resumo-ano" class="space-y-3">
            <h2 id="resumo-ano" class="text-xs font-semibold tracking-wider text-gray-500 uppercase dark:text-gray-400">
                Resumo de {{ $mes->year }}
            </h2>

            <div class="grid grid-cols-2 gap-3 md:gap-4 xl:grid-cols-4">
                @include('pages.dashboard.partials.kpi-card', [
                    'label' => 'Entrou no ano',
                    'value' => $resumoAno['entrou'],
                    'variant' => 'brand',
                    'icon' => 'entrada',
                ])
                @include('pages.dashboard.partials.kpi-card', [
                    'label' => 'Lucro líquido',
                    'value' => $resumoAno['lucro_liquido'],
                    'variant' => $resumoAno['lucro_liquido'] < 0 ? 'error' : 'success',
                    'icon' => 'lucro',
                ])
                @include('pages.dashboard.partials.kpi-card', [
                    'label' => 'Lucro bruto',
                    'value' => $resumoAno['lucro_bruto'],
                    'variant' => 'gray',
                    'icon' => 'bruto',
                    'hint' => 'Material: ' . Formatter::moneyShort($resumoAno['material']),
                ])
                @include('pages.dashboard.partials.kpi-card', [
                    'label' => 'Imposto',
                    'value' => $resumoAno['imposto'],
                    'variant' => 'warning',
                    'icon' => 'imposto',
                ])
            </div>

            @include('pages.dashboard.partials.chart-ano')
        </section>

        {{-- Projetos do mês --}}
        <section aria-labelledby="projetos-mes" class="space-y-3">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <h2 id="projetos-mes" class="text-xs font-semibold tracking-wider text-gray-500 uppercase dark:text-gray-400">
                    Entradas de {{ $mesLabel }}
                </h2>
            </div>

            <div class="grid grid-cols-2 gap-3 rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03] sm:grid-cols-4 sm:p-5">
                @foreach ([
                    ['Entrou', $resumoMes['entrou'], 'text-gray-800 dark:text-white/90'],
                    ['Material', $resumoMes['material'], 'text-gray-800 dark:text-white/90'],
                    ['Imposto', $resumoMes['imposto'], 'text-gray-800 dark:text-white/90'],
                    ['Lucro líquido', $resumoMes['lucro_liquido'], $resumoMes['lucro_liquido'] < 0 ? 'text-error-600 dark:text-error-500' : 'text-success-600 dark:text-success-500'],
                ] as [$rotulo, $valor, $cor])
                    <div>
                        <p class="text-theme-xs text-gray-500 dark:text-gray-400">{{ $rotulo }}</p>
                        <p class="mt-0.5 text-base font-semibold {{ $cor }}" title="{{ Formatter::money($valor) }}">
                            {{ Formatter::moneyShort($valor) }}
                        </p>
                    </div>
                @endforeach
            </div>

            @if (count($projetosDoMes))
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 2xl:grid-cols-3">
                    @foreach ($projetosDoMes as $projeto)
                        @include('pages.dashboard.partials.projeto-card', ['projeto' => $projeto])
                    @endforeach
                </div>
            @else
                <div class="rounded-2xl border border-dashed border-gray-300 bg-white px-6 py-10 text-center dark:border-gray-700 dark:bg-white/[0.03]">
                    <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M3 7.5h18M6.75 3.75h10.5A2.25 2.25 0 0 1 19.5 6v12a2.25 2.25 0 0 1-2.25 2.25H6.75A2.25 2.25 0 0 1 4.5 18V6a2.25 2.25 0 0 1 2.25-2.25ZM9 12h6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Nenhuma entrada em {{ $mesLabel }}.</p>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Os projetos aparecem aqui quando recebem algum pagamento no mês.</p>
                </div>
            @endif
        </section>
    </div>
@endsection
