@extends('layouts.app')

@section('content')
    @php
        // O ano atual é o padrão, então só conta como filtro quando foi alterado.
        $filtroAtivo = collect($filters)
            ->filter(fn ($v, $k) => $k === 'ano' ? $v !== $anoPadrao : $v !== '')
            ->isNotEmpty();
        $meses = [1 => 'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];
        $periodoLabel = $filters['ano'] === 'todos'
            ? 'Todos os períodos'
            : trim((($filters['mes'] !== '' && isset($meses[(int) $filters['mes']])) ? $meses[(int) $filters['mes']] . ' de ' : '') . $filters['ano']);
        $inputClass = 'dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30';
    @endphp

    <x-common.page-breadcrumb pageTitle="Projetos" />

    <div class="space-y-6">
        @include('pages.projetos.partials.flash')

        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex flex-col gap-4 border-b border-gray-100 px-6 py-5 dark:border-gray-800 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <h3 class="text-base font-medium text-gray-800 dark:text-white/90">Seus projetos</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Toque em um projeto para ver os números e lançar recebimentos e despesas.
                    </p>
                    <p class="mt-2 inline-flex items-center gap-1.5 rounded-full bg-brand-50 px-2.5 py-0.5 text-xs font-medium text-brand-600 dark:bg-brand-500/15 dark:text-brand-400">
                        Início em: {{ $periodoLabel }}
                    </p>
                </div>

                <button
                    type="button"
                    @click="$dispatch('open-create-projeto')"
                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600"
                >
                    <svg class="fill-current" width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M10 4.1665C10.4603 4.1665 10.8334 4.5396 10.8334 4.99984V9.1665H15C15.4603 9.1665 15.8334 9.5396 15.8334 9.99984C15.8334 10.4601 15.4603 10.8332 15 10.8332H10.8334V14.9998C10.8334 15.4601 10.4603 15.8332 10 15.8332C9.53978 15.8332 9.16669 15.4601 9.16669 14.9998V10.8332H5.00002C4.53978 10.8332 4.16669 10.4601 4.16669 9.99984C4.16669 9.5396 4.53978 9.1665 5.00002 9.1665H9.16669V4.99984C9.16669 4.5396 9.53978 4.1665 10 4.1665Z" fill="" />
                    </svg>
                    Novo projeto
                </button>
            </div>

            <div class="p-4 sm:p-6" x-data="{ filtros: @js($filtroAtivo) }">
                <form method="GET" action="{{ route('projetos.index') }}" class="space-y-4">
                    <div class="flex gap-3">
                        <div class="flex-1">
                            <label for="q" class="sr-only">Pesquisa</label>
                            <input id="q" type="text" name="q" value="{{ $filters['q'] }}" placeholder="Buscar por projeto ou cliente" class="{{ $inputClass }}" />
                        </div>
                        <button type="button" @click="filtros = !filtros"
                            class="inline-flex h-11 shrink-0 items-center gap-2 rounded-lg border border-gray-300 bg-white px-3 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]"
                            :aria-expanded="filtros">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M4 6h16M7 12h10M10 18h4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" />
                            </svg>
                            <span class="hidden sm:inline">Filtros</span>
                        </button>
                        <button type="submit" class="inline-flex h-11 shrink-0 items-center justify-center rounded-lg bg-brand-500 px-4 text-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600">
                            Buscar
                        </button>
                    </div>

                    <div x-show="filtros" x-cloak class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5 lg:items-end">
                        <div>
                            <label for="ano" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Ano de início</label>
                            <select id="ano" name="ano" class="{{ $inputClass }}">
                                @foreach ($anos as $ano)
                                    <option value="{{ $ano }}" @selected($filters['ano'] === (string) $ano)>{{ $ano }}</option>
                                @endforeach
                                <option value="todos" @selected($filters['ano'] === 'todos')>Todos os anos</option>
                            </select>
                        </div>
                        <div>
                            <label for="mes" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Mês de início</label>
                            <select id="mes" name="mes" class="{{ $inputClass }}">
                                <option value="">Todos</option>
                                @foreach ($meses as $num => $nome)
                                    <option value="{{ $num }}" @selected($filters['mes'] === (string) $num)>{{ $nome }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="status" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Situação</label>
                            <select id="status" name="status" class="{{ $inputClass }}">
                                <option value="">Todas</option>
                                @foreach (\App\Enums\StatusProjeto::options() as $value => $label)
                                    <option value="{{ $value }}" @selected($filters['status'] === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="tipo" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Tipo</label>
                            <select id="tipo" name="tipo" class="{{ $inputClass }}">
                                <option value="">Todos</option>
                                @foreach ($tipos as $tipo)
                                    <option value="{{ $tipo->id }}" @selected($filters['tipo'] === (string) $tipo->id)>{{ $tipo->nome }}</option>
                                @endforeach
                            </select>
                        </div>
                        @if ($filtroAtivo)
                            <a href="{{ route('projetos.index') }}"
                                class="inline-flex h-11 items-center justify-center rounded-lg border border-gray-300 bg-white px-4 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                                Limpar filtros
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        @if ($projetos->count())
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 2xl:grid-cols-3">
                @foreach ($projetos as $projeto)
                    @include('pages.projetos.partials.projeto-card', ['projeto' => $projeto])
                @endforeach
            </div>

            @if ($projetos->hasPages())
                <div>{{ $projetos->links() }}</div>
            @endif
        @else
            <div class="rounded-2xl border border-dashed border-gray-300 bg-white px-6 py-10 text-center dark:border-gray-700 dark:bg-white/[0.03]">
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Nenhum projeto encontrado.</p>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Ajuste os filtros ou cadastre um novo projeto.</p>
            </div>
        @endif
    </div>

    @include('pages.projetos.partials.create-modal')
@endsection
