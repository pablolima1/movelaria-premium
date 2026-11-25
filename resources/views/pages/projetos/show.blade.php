@extends('layouts.app')

@use('App\Helpers\Formatter')
@use('App\Enums\StatusProjeto')
@use('App\Enums\StatusDespesa')
@use('App\Enums\FormaPagamento')
@use('App\Enums\CategoriaDespesa')

@php
    $f = $projeto->financeiro;
    $status = StatusProjeto::tryFrom((string) $projeto->status);
    $abaInicial = old('_modal') === 'despesa' ? 'despesas' : (old('_modal') === 'recebimento' ? 'recebimentos' : null);
    $iconBtn = 'inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg border border-gray-300 bg-white text-gray-600 transition hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200';
    $iconBtnDanger = 'inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg border border-red-200 bg-white text-red-600 transition hover:bg-red-50 dark:border-red-500/30 dark:bg-gray-800 dark:text-red-400 dark:hover:bg-red-500/10';
    $editIcon = '<svg class="fill-current" width="16" height="16" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M15.0911 2.78206C14.2125 1.90338 12.7878 1.90338 11.9092 2.78206L4.57524 10.116C4.26682 10.4244 4.0547 10.8158 3.96468 11.2426L3.31231 14.3352C3.25997 14.5833 3.33653 14.841 3.51583 15.0203C3.69512 15.1996 3.95286 15.2761 4.20096 15.2238L7.29355 14.5714C7.72031 14.4814 8.11172 14.2693 8.42013 13.9609L15.7541 6.62695C16.6327 5.74827 16.6327 4.32365 15.7541 3.44497L15.0911 2.78206ZM12.9698 3.84272C13.2627 3.54982 13.7376 3.54982 14.0305 3.84272L14.6934 4.50563C14.9863 4.79852 14.9863 5.2734 14.6934 5.56629L14.044 6.21573L12.3204 4.49215L12.9698 3.84272ZM11.2597 5.55281L5.6359 11.1766C5.53309 11.2794 5.46238 11.4099 5.43238 11.5522L5.01758 13.5185L6.98394 13.1037C7.1262 13.0737 7.25666 13.003 7.35947 12.9002L12.9833 7.27639L11.2597 5.55281Z" fill="" /></svg>';
    $trashIcon = '<svg class="stroke-current" width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M19 7L18.1327 19.1425C18.0579 20.1891 17.187 21 16.1378 21H7.86224C6.81296 21 5.94208 20.1891 5.86732 19.1425L5 7M10 11V17M14 11V17M15 7V4C15 3.44772 14.5523 3 14 3H10C9.44772 3 9 3.44772 9 4V7M4 7H20" stroke="" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" /></svg>';
    $plusIcon = '<svg class="fill-current" width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M10 4.1665C10.4603 4.1665 10.8334 4.5396 10.8334 4.99984V9.1665H15C15.4603 9.1665 15.8334 9.5396 15.8334 9.99984C15.8334 10.4601 15.4603 10.8332 15 10.8332H10.8334V14.9998C10.8334 15.4601 10.4603 15.8332 10 15.8332C9.53978 15.8332 9.16669 15.4601 9.16669 14.9998V10.8332H5.00002C4.53978 10.8332 4.16669 10.4601 4.16669 9.99984C4.16669 9.5396 4.53978 9.1665 5.00002 9.1665H9.16669V4.99984C9.16669 4.5396 9.53978 4.1665 10 4.1665Z" fill="" /></svg>';
@endphp

@section('content')
    <div class="space-y-6">
        <div class="flex items-center justify-between gap-3">
            <a href="{{ route('projetos.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-gray-500 transition hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200">
                <svg width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12.5 15 7.5 10l5-5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" /></svg>
                Projetos
            </a>
        </div>

        @include('pages.projetos.partials.flash')

        {{-- Cabeçalho --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] sm:p-6">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <h1 class="text-xl font-semibold text-gray-800 dark:text-white/90">{{ $projeto->nome }}</h1>
                        @if ($status)
                            <span class="rounded-full px-2.5 py-0.5 text-theme-xs font-medium {{ $status->badgeClass() }}">{{ $status->label() }}</span>
                        @endif
                    </div>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        {{ $projeto->cliente?->nome ?? '—' }}
                        @if ($projeto->tipoProjeto) · {{ $projeto->tipoProjeto->nome }} @endif
                    </p>
                    <p class="mt-1 text-theme-xs text-gray-500 dark:text-gray-400">
                        Início: {{ Formatter::date($projeto->data_inicio) }}
                        @if ($projeto->data_conclusao) · Conclusão: {{ Formatter::date($projeto->data_conclusao) }} @endif
                    </p>
                    <p class="mt-1 text-theme-xs text-gray-500 dark:text-gray-400">
                        Pagamento:
                        <span class="font-medium text-gray-700 dark:text-gray-300">
                            @if ($projeto->isParcelado())
                                Parcelado em {{ $projeto->numero_parcelas }}x @if ($projeto->valorParcela()) de {{ Formatter::money($projeto->valorParcela()) }} @endif
                            @else
                                À vista
                            @endif
                        </span>
                    </p>
                </div>
                <div class="flex shrink-0 items-center gap-2">
                    <button type="button" @click="$dispatch('open-edit-projeto')" class="{{ $iconBtn }}" title="Editar projeto">{!! $editIcon !!}</button>
                    <form method="POST" action="{{ route('projetos.destroy', $projeto) }}" onsubmit="return confirm('Tem certeza que deseja excluir este projeto?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="{{ $iconBtnDanger }}" title="Excluir projeto">{!! $trashIcon !!}</button>
                    </form>
                </div>
            </div>

            @if ($projeto->observacoes)
                <p class="mt-4 rounded-xl bg-gray-50 p-3 text-sm whitespace-pre-line text-gray-600 dark:bg-white/[0.03] dark:text-gray-300">{{ $projeto->observacoes }}</p>
            @endif
        </div>

        {{-- Resumo financeiro --}}
        <div class="grid grid-cols-2 gap-3 md:gap-4 lg:grid-cols-3">
            <div class="col-span-2 rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03] sm:p-5 lg:col-span-1">
                <p class="text-theme-xs text-gray-500 sm:text-sm dark:text-gray-400">Valor total</p>
                <p class="mt-1 text-2xl font-bold text-gray-800 dark:text-white/90">{{ Formatter::money($f['valor_total']) }}</p>
                @if (! is_null($f['progresso']))
                    <div class="mt-3 h-2 w-full overflow-hidden rounded-full bg-gray-200 dark:bg-gray-800">
                        <div class="h-full rounded-full bg-brand-500" style="width: {{ $f['progresso'] }}%"></div>
                    </div>
                    <p class="mt-1.5 text-theme-xs text-gray-500 dark:text-gray-400">{{ $f['progresso'] }}% recebido</p>
                @else
                    <p class="mt-2 text-theme-xs text-warning-600 dark:text-orange-400">Defina o valor total para calcular imposto e lucro.</p>
                @endif
            </div>

            @foreach ([
                ['Recebido', $f['recebido_total'], 'text-gray-800 dark:text-white/90', null],
                ['A receber', $f['a_receber'], 'text-gray-800 dark:text-white/90', null],
                ['Despesas', $f['material'], 'text-gray-800 dark:text-white/90', null],
                ['Imposto', $f['imposto'], 'text-gray-800 dark:text-white/90', is_null($f['aliquota']) ? 'Sem alíquota vigente' : Formatter::percent($f['aliquota']) . ' do valor total'],
            ] as [$rotulo, $valor, $cor, $hint])
                <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03] sm:p-5">
                    <p class="text-theme-xs text-gray-500 sm:text-sm dark:text-gray-400">{{ $rotulo }}</p>
                    <p class="mt-1 text-base font-semibold sm:text-xl {{ $cor }}">{{ Formatter::money($valor) }}</p>
                    @if ($hint)
                        <p class="mt-0.5 text-theme-xs text-gray-500 dark:text-gray-400">{{ $hint }}</p>
                    @endif
                </div>
            @endforeach

            <div class="rounded-2xl border p-4 sm:p-5 {{ $f['lucro_liquido'] < 0 ? 'border-error-200 bg-error-50 dark:border-error-500/30 dark:bg-error-500/10' : 'border-success-200 bg-success-50 dark:border-success-500/30 dark:bg-success-500/10' }}">
                <p class="text-theme-xs sm:text-sm {{ $f['lucro_liquido'] < 0 ? 'text-error-700 dark:text-error-400' : 'text-success-700 dark:text-success-400' }}">Lucro líquido</p>
                <p class="mt-1 text-base font-bold sm:text-xl {{ $f['lucro_liquido'] < 0 ? 'text-error-700 dark:text-error-400' : 'text-success-700 dark:text-success-400' }}">{{ Formatter::money($f['lucro_liquido']) }}</p>
                <p class="mt-0.5 text-theme-xs {{ $f['lucro_liquido'] < 0 ? 'text-error-600/80 dark:text-error-400/80' : 'text-success-600/80 dark:text-success-400/80' }}">total − despesas − imposto</p>
            </div>
        </div>

        {{-- Parcelas (projeto parcelado) --}}
        @if ($projeto->parcelas->isNotEmpty())
            @php
                $parcelasPagas = $projeto->parcelas->filter->isPaga();
                $parcelasAtrasadas = $projeto->parcelas->filter(fn ($p) => $p->diasAtraso() > 0);
            @endphp
            <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03] sm:p-6">
                <div class="flex flex-wrap items-baseline justify-between gap-2">
                    <h3 class="text-base font-medium text-gray-800 dark:text-white/90">Parcelas</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ $parcelasPagas->count() }} de {{ $projeto->parcelas->count() }} pagas
                        @if ($parcelasAtrasadas->isNotEmpty())
                            · <span class="font-medium text-error-600 dark:text-error-500">{{ $parcelasAtrasadas->count() }} em atraso</span>
                        @endif
                    </p>
                </div>
                <ul class="mt-2 divide-y divide-gray-100 dark:divide-gray-800">
                    @foreach ($projeto->parcelas as $parcela)
                        @include('pages.cobrancas.partials.parcela-item', ['parcela' => $parcela])
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Lançamentos --}}
        <div
            x-data="{
                key: 'projeto-aba-{{ $projeto->id }}',
                tab: 'recebimentos',
                init() {
                    this.tab = @js($abaInicial) || sessionStorage.getItem(this.key) || 'recebimentos';
                    this.$watch('tab', v => sessionStorage.setItem(this.key, v));
                }
            }"
            class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]"
        >
            <div class="flex items-center gap-2 border-b border-gray-100 p-2 dark:border-gray-800">
                @foreach (['recebimentos' => ['Recebimentos', $projeto->recebimentos->count()], 'despesas' => ['Despesas', $projeto->despesas->count()]] as $aba => [$titulo, $qtd])
                    <button type="button" @click="tab = '{{ $aba }}'"
                        class="flex flex-1 items-center justify-center gap-2 rounded-lg px-3 py-2.5 text-sm font-medium transition"
                        :class="tab === '{{ $aba }}' ? 'bg-gray-100 text-gray-800 dark:bg-white/[0.06] dark:text-white/90' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200'">
                        {{ $titulo }}
                        <span class="rounded-full bg-white px-2 py-0.5 text-theme-xs text-gray-600 shadow-theme-xs dark:bg-gray-800 dark:text-gray-300">{{ $qtd }}</span>
                    </button>
                @endforeach
            </div>

            {{-- Recebimentos --}}
            <div x-show="tab === 'recebimentos'" class="p-4 sm:p-6">
                <div class="mb-4 flex items-center justify-between gap-3">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Total recebido: <span class="font-semibold text-gray-800 dark:text-white/90">{{ Formatter::money($f['recebido_total']) }}</span>
                    </p>
                    <button type="button" @click="$dispatch('open-create-recebimento')"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-brand-500 px-3 py-2 text-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600">
                        {!! $plusIcon !!} Recebimento
                    </button>
                </div>

                <ul class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse ($projeto->recebimentos as $recebimento)
                        <li class="flex items-center gap-3 py-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-success-50 text-success-600 dark:bg-success-500/15 dark:text-success-500">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M12 4.5v15m0 0-5.25-5.25M12 19.5l5.25-5.25" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="font-semibold text-gray-800 dark:text-white/90">{{ Formatter::money($recebimento->valor) }}</p>
                                <p class="truncate text-theme-xs text-gray-500 dark:text-gray-400">
                                    {{ Formatter::date($recebimento->data_recebimento) }} · {{ FormaPagamento::labelFor($recebimento->forma_pagamento) }}
                                    @if ($recebimento->observacoes) · {{ $recebimento->observacoes }} @endif
                                </p>
                            </div>
                            <div class="flex shrink-0 items-center gap-2">
                                <button type="button" class="{{ $iconBtn }}" title="Editar recebimento"
                                    @click="$dispatch('open-edit-recebimento', @js([
                                        'id' => $recebimento->id,
                                        'valor' => Formatter::moneyInput($recebimento->valor),
                                        'data_recebimento' => $recebimento->data_recebimento->format('Y-m-d'),
                                        'forma_pagamento' => FormaPagamento::tryFrom($recebimento->forma_pagamento) ? $recebimento->forma_pagamento : 'pix',
                                        'observacoes' => $recebimento->observacoes ?? '',
                                    ]))">{!! $editIcon !!}</button>
                                <form method="POST" action="{{ route('projetos.recebimentos.destroy', [$projeto, $recebimento]) }}" onsubmit="return confirm('Excluir este recebimento?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="{{ $iconBtnDanger }}" title="Excluir recebimento">{!! $trashIcon !!}</button>
                                </form>
                            </div>
                        </li>
                    @empty
                        <li class="py-8 text-center">
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Nenhum recebimento lançado.</p>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Lance a entrada e as parcelas conforme o cliente pagar.</p>
                        </li>
                    @endforelse
                </ul>
            </div>

            {{-- Despesas --}}
            <div x-show="tab === 'despesas'" x-cloak class="p-4 sm:p-6">
                <div class="mb-4 flex items-center justify-between gap-3">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Total de despesas: <span class="font-semibold text-gray-800 dark:text-white/90">{{ Formatter::money($f['material']) }}</span>
                    </p>
                    <button type="button" @click="$dispatch('open-create-despesa')"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-brand-500 px-3 py-2 text-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600">
                        {!! $plusIcon !!} Despesa
                    </button>
                </div>

                <ul class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse ($projeto->despesas as $despesa)
                        @php($statusDespesa = StatusDespesa::tryFrom((string) $despesa->status))
                        <li class="flex items-center gap-3 py-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-300">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M12 19.5v-15m0 0L6.75 9.75M12 4.5l5.25 5.25" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-x-2 gap-y-0.5">
                                    <p class="font-semibold text-gray-800 dark:text-white/90">{{ Formatter::money($despesa->valor) }}</p>
                                    @if ($statusDespesa)
                                        <span class="rounded-full px-2 py-0.5 text-[11px] font-medium {{ $statusDespesa->badgeClass() }}">{{ $statusDespesa->label() }}</span>
                                    @endif
                                </div>
                                <p class="truncate text-theme-xs text-gray-500 dark:text-gray-400">
                                    {{ Formatter::date($despesa->data_despesa) }} · {{ CategoriaDespesa::labelFor($despesa->categoria) }}
                                    @if ($despesa->descricao) · {{ $despesa->descricao }} @endif
                                </p>
                            </div>
                            <div class="flex shrink-0 items-center gap-2">
                                <button type="button" class="{{ $iconBtn }}" title="Editar despesa"
                                    @click="$dispatch('open-edit-despesa', @js([
                                        'id' => $despesa->id,
                                        'valor' => Formatter::moneyInput($despesa->valor),
                                        'categoria' => CategoriaDespesa::tryFrom($despesa->categoria) ? $despesa->categoria : 'outros',
                                        'descricao' => $despesa->descricao ?? '',
                                        'data_despesa' => $despesa->data_despesa->format('Y-m-d'),
                                        'status' => $despesa->status ?: 'pendente',
                                        'data_pagamento' => $despesa->data_pagamento?->format('Y-m-d') ?? '',
                                        'forma_pagamento' => $despesa->forma_pagamento ?? '',
                                    ]))">{!! $editIcon !!}</button>
                                <form method="POST" action="{{ route('projetos.despesas.destroy', [$projeto, $despesa]) }}" onsubmit="return confirm('Excluir esta despesa?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="{{ $iconBtnDanger }}" title="Excluir despesa">{!! $trashIcon !!}</button>
                                </form>
                            </div>
                        </li>
                    @empty
                        <li class="py-8 text-center">
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Nenhuma despesa lançada.</p>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Lance as compras de material e outros custos do projeto.</p>
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>

    @include('pages.projetos.partials.edit-modal')
    @include('pages.projetos.partials.recebimento-modal')
    @include('pages.projetos.partials.despesa-modal')
    @include('pages.cobrancas.partials.modals')
@endsection
