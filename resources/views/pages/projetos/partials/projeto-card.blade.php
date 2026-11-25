@use('App\Helpers\Formatter')
@use('App\Enums\StatusProjeto')

@php
    $f = $projeto->financeiro;
    $status = StatusProjeto::tryFrom((string) $projeto->status);
@endphp

<a href="{{ route('projetos.show', $projeto) }}"
    class="group flex flex-col rounded-2xl border border-gray-200 bg-white p-5 transition hover:border-brand-300 hover:shadow-theme-sm dark:border-gray-800 dark:bg-white/[0.03] dark:hover:border-brand-800">
    <div class="flex items-start justify-between gap-3">
        <div class="min-w-0">
            <h3 class="truncate text-base font-semibold text-gray-800 group-hover:text-brand-600 dark:text-white/90 dark:group-hover:text-brand-400">
                {{ $projeto->nome }}
            </h3>
            <p class="mt-0.5 truncate text-sm text-gray-500 dark:text-gray-400">
                {{ $projeto->cliente?->nome ?? '—' }}
                @if ($projeto->tipoProjeto) · {{ $projeto->tipoProjeto->nome }} @endif
            </p>
        </div>
        @if ($status)
            <span class="shrink-0 rounded-full px-2.5 py-0.5 text-theme-xs font-medium {{ $status->badgeClass() }}">{{ $status->label() }}</span>
        @endif
    </div>

    <div class="mt-3 grid grid-cols-2 gap-2 rounded-xl bg-gray-50 px-3 py-2 text-theme-xs dark:bg-white/[0.03]">
        <div>
            <span class="block text-gray-500 dark:text-gray-400">Início</span>
            <span class="font-medium text-gray-800 dark:text-white/90">{{ Formatter::date($projeto->data_inicio) }}</span>
        </div>
        <div>
            <span class="block text-gray-500 dark:text-gray-400">Conclusão</span>
            <span class="font-medium text-gray-800 dark:text-white/90">{{ Formatter::date($projeto->data_conclusao) }}</span>
        </div>
    </div>

    <div class="mt-4 flex items-baseline justify-between gap-2">
        <span class="text-theme-xs text-gray-500 dark:text-gray-400">Valor total</span>
        <span class="text-lg font-bold text-gray-800 dark:text-white/90">{{ Formatter::money($f['valor_total']) }}</span>
    </div>
    @if ($projeto->isParcelado())
        <p class="mt-0.5 text-right text-theme-xs text-gray-500 dark:text-gray-400">
            Parcelado em {{ $projeto->numero_parcelas }}x @if ($projeto->valorParcela()) de {{ Formatter::money($projeto->valorParcela()) }} @endif
        </p>
    @endif

    @if (! is_null($f['progresso']))
        <div class="mt-2 h-1.5 w-full overflow-hidden rounded-full bg-gray-200 dark:bg-gray-800">
            <div class="h-full rounded-full bg-brand-500" style="width: {{ $f['progresso'] }}%"></div>
        </div>
        <p class="mt-1.5 text-theme-xs text-gray-500 dark:text-gray-400">
            {{ $f['progresso'] }}% recebido · faltam {{ Formatter::money($f['a_receber']) }}
        </p>
    @else
        <p class="mt-2 text-theme-xs text-gray-500 dark:text-gray-400">Valor total ainda não definido.</p>
    @endif

    <div class="mt-4 flex items-center justify-between gap-2 border-t border-gray-100 pt-3 text-sm dark:border-gray-800">
        <span class="text-gray-500 dark:text-gray-400">Lucro líquido previsto</span>
        <span class="font-semibold {{ $f['lucro_liquido'] < 0 ? 'text-error-600 dark:text-error-500' : 'text-success-600 dark:text-success-500' }}">
            {{ Formatter::money($f['lucro_liquido']) }}
        </span>
    </div>
</a>
