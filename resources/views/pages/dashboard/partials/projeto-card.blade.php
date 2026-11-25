@use('App\Helpers\Formatter')

@php
    $negativo = $projeto['lucro_liquido'] < 0;
    $statusLabel = $projeto['status'] ? ucfirst(str_replace('_', ' ', $projeto['status'])) : null;
@endphp

<article class="flex flex-col rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
    <header class="flex items-start justify-between gap-3">
        <div class="min-w-0">
            <h3 class="truncate text-base font-semibold text-gray-800 dark:text-white/90" title="{{ $projeto['nome'] }}">
                {{ $projeto['nome'] }}
            </h3>
            <p class="mt-0.5 truncate text-sm text-gray-500 dark:text-gray-400">{{ $projeto['cliente'] ?? '—' }}</p>
        </div>
        @if ($projeto['tipo'])
            <span class="shrink-0 rounded-full bg-brand-50 px-2.5 py-0.5 text-theme-xs font-medium text-brand-600 dark:bg-brand-500/15 dark:text-brand-400">
                {{ $projeto['tipo'] }}
            </span>
        @endif
    </header>

    <div class="mt-4 rounded-xl bg-gray-50 p-3 dark:bg-white/[0.03]">
        <div class="flex items-baseline justify-between gap-2">
            <span class="text-theme-xs text-gray-500 dark:text-gray-400">Entrou no mês</span>
            <span class="text-lg font-bold text-gray-800 dark:text-white/90">{{ Formatter::money($projeto['recebido_mes']) }}</span>
        </div>
        @if (! is_null($projeto['progresso']))
            <div class="mt-2 h-1.5 w-full overflow-hidden rounded-full bg-gray-200 dark:bg-gray-800">
                <div class="h-full rounded-full bg-brand-500" style="width: {{ $projeto['progresso'] }}%"></div>
            </div>
            <p class="mt-1.5 text-theme-xs text-gray-500 dark:text-gray-400">
                {{ $projeto['progresso'] }}% recebido · {{ Formatter::money($projeto['recebido_total']) }} de {{ Formatter::money($projeto['valor_total']) }}
            </p>
        @endif
    </div>

    <dl class="mt-4 space-y-2 text-sm">
        <div class="flex justify-between gap-2">
            <dt class="text-gray-500 dark:text-gray-400">Valor total</dt>
            <dd class="font-medium text-gray-800 dark:text-white/90">{{ Formatter::money($projeto['valor_total']) }}</dd>
        </div>
        <div class="flex justify-between gap-2">
            <dt class="text-gray-500 dark:text-gray-400">Material</dt>
            <dd class="font-medium text-gray-800 dark:text-white/90">− {{ Formatter::money($projeto['material']) }}</dd>
        </div>
        <div class="flex justify-between gap-2">
            <dt class="text-gray-500 dark:text-gray-400">
                Imposto
                @if (! is_null($projeto['aliquota']))
                    <span class="text-theme-xs">({{ Formatter::percent($projeto['aliquota']) }})</span>
                @endif
            </dt>
            <dd class="font-medium text-gray-800 dark:text-white/90">− {{ Formatter::money($projeto['imposto']) }}</dd>
        </div>
        <div class="flex items-center justify-between gap-2 border-t border-gray-100 pt-2 dark:border-gray-800">
            <dt class="font-medium text-gray-700 dark:text-gray-300">Lucro líquido</dt>
            <dd class="text-base font-bold {{ $negativo ? 'text-error-600 dark:text-error-500' : 'text-success-600 dark:text-success-500' }}">
                {{ Formatter::money($projeto['lucro_liquido']) }}
            </dd>
        </div>
    </dl>

    @if (is_null($projeto['aliquota']) || $statusLabel)
        <footer class="mt-3 flex flex-wrap items-center gap-2">
            @if ($statusLabel)
                <span class="rounded-full bg-gray-100 px-2.5 py-0.5 text-theme-xs font-medium text-gray-600 dark:bg-white/5 dark:text-gray-300">{{ $statusLabel }}</span>
            @endif
            @if (is_null($projeto['aliquota']))
                <span class="rounded-full bg-warning-50 px-2.5 py-0.5 text-theme-xs font-medium text-warning-700 dark:bg-warning-500/15 dark:text-orange-400">
                    Sem alíquota vigente na data do projeto
                </span>
            @endif
        </footer>
    @endif
</article>
