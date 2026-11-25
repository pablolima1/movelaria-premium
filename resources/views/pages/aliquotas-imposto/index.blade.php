@extends('layouts.app')

@section('content')
    @php
        $formatPercentual = fn ($value) => is_numeric($value) ? number_format((float) $value, 2, ',', '.') : (string) $value;
        $createModalOpen = $errors->any() && old('_modal') === 'create';
        $editModalOpen = $errors->any() && old('_modal') === 'edit';
        $editOldAliquota = $editModalOpen ? [
            'id' => old('_aliquota_id'),
            'percentual' => $formatPercentual(old('percentual', '')),
            'vigente_desde' => old('vigente_desde', ''),
            'vigente_ate' => old('vigente_ate', '') ?? '',
        ] : null;
        $situacoes = [
            'vigente' => ['label' => 'Vigente', 'class' => 'bg-success-50 text-success-700 dark:bg-success-500/15 dark:text-success-500'],
            'futura' => ['label' => 'Futura', 'class' => 'bg-blue-light-50 text-blue-light-500 dark:bg-blue-light-500/15 dark:text-blue-light-500'],
            'encerrada' => ['label' => 'Encerrada', 'class' => 'bg-gray-100 text-gray-700 dark:bg-white/5 dark:text-white/80'],
        ];
    @endphp

    <div
        x-data="{
            init() {
                @if($createModalOpen)
                    this.$dispatch('open-create-aliquota');
                @endif
                @if($editModalOpen && $editOldAliquota && $editOldAliquota['id'])
                    this.$dispatch('open-edit-aliquota', @js($editOldAliquota));
                @endif
            }
        }"
    >
        <x-common.page-breadcrumb pageTitle="Alíquotas de imposto" />

        <div class="space-y-6">
            @if (session('success'))
                <div class="rounded-xl border border-green-200 bg-green-50 p-4 text-sm text-green-700 dark:border-green-500/30 dark:bg-green-500/10 dark:text-green-400">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700 dark:border-red-500/30 dark:bg-red-500/10 dark:text-red-400">
                    {{ session('error') }}
                </div>
            @endif

            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] sm:p-6">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Alíquota vigente hoje</p>
                        @if ($aliquotaVigente)
                            <h4 class="mt-1 text-title-sm font-bold text-gray-800 dark:text-white/90">
                                {{ $formatPercentual($aliquotaVigente->percentual) }}%
                            </h4>
                        @else
                            <h4 class="mt-1 text-lg font-semibold text-warning-600 dark:text-warning-500">
                                Nenhuma alíquota vigente
                            </h4>
                        @endif
                    </div>
                    @if ($aliquotaVigente)
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Desde {{ $aliquotaVigente->vigente_desde->format('d/m/Y') }}
                            {{ $aliquotaVigente->vigente_ate ? 'até ' . $aliquotaVigente->vigente_ate->format('d/m/Y') : '(sem data final)' }}
                        </p>
                    @else
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Cadastre uma alíquota com vigência que inclua a data de hoje.
                        </p>
                    @endif
                </div>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="flex flex-col gap-4 border-b border-gray-100 px-6 py-5 dark:border-gray-800 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <h3 class="text-base font-medium text-gray-800 dark:text-white/90">Listagem de alíquotas</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Gerencie o histórico de alíquotas de imposto e seus períodos de vigência.
                        </p>
                    </div>

                    <button
                        type="button"
                        @click="$dispatch('open-create-aliquota')"
                        class="inline-flex items-center justify-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600"
                    >
                        <svg class="fill-current" width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M10 4.1665C10.4603 4.1665 10.8334 4.5396 10.8334 4.99984V9.1665H15C15.4603 9.1665 15.8334 9.5396 15.8334 9.99984C15.8334 10.4601 15.4603 10.8332 15 10.8332H10.8334V14.9998C10.8334 15.4601 10.4603 15.8332 10 15.8332C9.53978 15.8332 9.16669 15.4601 9.16669 14.9998V10.8332H5.00002C4.53978 10.8332 4.16669 10.4601 4.16669 9.99984C4.16669 9.5396 4.53978 9.1665 5.00002 9.1665H9.16669V4.99984C9.16669 4.5396 9.53978 4.1665 10 4.1665Z" fill="" />
                        </svg>
                        Nova alíquota
                    </button>
                </div>

                <div class="border-b border-gray-100 p-4 dark:border-gray-800 sm:p-6">
                    <form method="GET" action="{{ route('aliquotas-imposto.index') }}" class="grid grid-cols-1 gap-4 md:grid-cols-12 md:items-end">
                        <div class="md:col-span-9">
                            <label for="status" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                Situação
                            </label>
                            <select
                                id="status"
                                name="status"
                                class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                            >
                                <option value="" @selected(($filters['status'] ?? '') === '')>Todas</option>
                                <option value="vigente" @selected(($filters['status'] ?? '') === 'vigente')>Vigente</option>
                                <option value="futura" @selected(($filters['status'] ?? '') === 'futura')>Futuras</option>
                                <option value="encerrada" @selected(($filters['status'] ?? '') === 'encerrada')>Encerradas</option>
                            </select>
                        </div>

                        <div class="flex flex-col gap-3 sm:flex-row md:col-span-3">
                            <button type="submit"
                                class="inline-flex h-11 w-full items-center justify-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600 sm:w-auto">
                                Filtrar
                            </button>
                            @if(($filters['status'] ?? '') !== '')
                                <a href="{{ route('aliquotas-imposto.index') }}"
                                    class="inline-flex h-11 w-full items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] sm:w-auto">
                                    Limpar
                                </a>
                            @endif
                        </div>
                    </form>
                </div>

                <div class="p-4 sm:p-6">
                    <div class="overflow-hidden rounded-xl border border-gray-200 dark:border-gray-800">
                        <div class="max-w-full overflow-x-auto custom-scrollbar">
                            <table class="w-full min-w-[640px]">
                                <thead>
                                    <tr class="border-b border-gray-100 bg-gray-50 dark:border-gray-800 dark:bg-gray-900/40">
                                        <th class="px-5 py-3 text-left sm:px-6">
                                            <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Percentual</p>
                                        </th>
                                        <th class="px-5 py-3 text-left sm:px-6">
                                            <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Vigente desde</p>
                                        </th>
                                        <th class="px-5 py-3 text-left sm:px-6">
                                            <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Vigente até</p>
                                        </th>
                                        <th class="px-5 py-3 text-left sm:px-6">
                                            <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Situação</p>
                                        </th>
                                        <th class="px-5 py-3 text-right sm:px-6">
                                            <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Ações</p>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($aliquotas as $aliquota)
                                        @php($situacao = $situacoes[$aliquota->situacao()])
                                        <tr class="border-b border-gray-100 last:border-b-0 dark:border-gray-800">
                                            <td class="px-5 py-4 sm:px-6">
                                                <p class="font-medium text-gray-800 text-theme-sm dark:text-white/90">{{ $formatPercentual($aliquota->percentual) }}%</p>
                                            </td>
                                            <td class="px-5 py-4 sm:px-6">
                                                <p class="text-gray-600 text-theme-sm dark:text-gray-300">{{ $aliquota->vigente_desde->format('d/m/Y') }}</p>
                                            </td>
                                            <td class="px-5 py-4 sm:px-6">
                                                <p class="text-gray-600 text-theme-sm dark:text-gray-300">{{ $aliquota->vigente_ate?->format('d/m/Y') ?? 'Sem data final' }}</p>
                                            </td>
                                            <td class="px-5 py-4 sm:px-6">
                                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-theme-xs font-medium {{ $situacao['class'] }}">
                                                    {{ $situacao['label'] }}
                                                </span>
                                            </td>
                                            <td class="px-5 py-4 sm:px-6">
                                                <div class="flex items-center justify-end gap-2">
                                                    <button
                                                        type="button"
                                                        @click="$dispatch('open-edit-aliquota', @js([
                                                            'id' => $aliquota->id,
                                                            'percentual' => $formatPercentual($aliquota->percentual),
                                                            'vigente_desde' => $aliquota->vigente_desde->format('Y-m-d'),
                                                            'vigente_ate' => $aliquota->vigente_ate?->format('Y-m-d') ?? '',
                                                        ]))"
                                                        class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-300 bg-white text-gray-600 transition hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200"
                                                        title="Editar alíquota"
                                                    >
                                                        <svg class="fill-current" width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M15.0911 2.78206C14.2125 1.90338 12.7878 1.90338 11.9092 2.78206L4.57524 10.116C4.26682 10.4244 4.0547 10.8158 3.96468 11.2426L3.31231 14.3352C3.25997 14.5833 3.33653 14.841 3.51583 15.0203C3.69512 15.1996 3.95286 15.2761 4.20096 15.2238L7.29355 14.5714C7.72031 14.4814 8.11172 14.2693 8.42013 13.9609L15.7541 6.62695C16.6327 5.74827 16.6327 4.32365 15.7541 3.44497L15.0911 2.78206ZM12.9698 3.84272C13.2627 3.54982 13.7376 3.54982 14.0305 3.84272L14.6934 4.50563C14.9863 4.79852 14.9863 5.2734 14.6934 5.56629L14.044 6.21573L12.3204 4.49215L12.9698 3.84272ZM11.2597 5.55281L5.6359 11.1766C5.53309 11.2794 5.46238 11.4099 5.43238 11.5522L5.01758 13.5185L6.98394 13.1037C7.1262 13.0737 7.25666 13.003 7.35947 12.9002L12.9833 7.27639L11.2597 5.55281Z" fill="" />
                                                        </svg>
                                                    </button>

                                                    <form method="POST" action="{{ route('aliquotas-imposto.destroy', $aliquota) }}" onsubmit="return confirm('Tem certeza que deseja excluir esta alíquota?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <input type="hidden" name="status" value="{{ $filters['status'] ?? '' }}">
                                                        <button
                                                            type="submit"
                                                            class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-red-200 bg-white text-red-600 transition hover:bg-red-50 dark:border-red-500/30 dark:bg-gray-800 dark:text-red-400 dark:hover:bg-red-500/10"
                                                            title="Excluir alíquota"
                                                        >
                                                            <svg class="stroke-current" width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M19 7L18.1327 19.1425C18.0579 20.1891 17.187 21 16.1378 21H7.86224C6.81296 21 5.94208 20.1891 5.86732 19.1425L5 7M10 11V17M14 11V17M15 7V4C15 3.44772 14.5523 3 14 3H10C9.44772 3 9 3.44772 9 4V7M4 7H20" stroke="" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                                                            </svg>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-5 py-10 text-center sm:px-6">
                                                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Nenhuma alíquota encontrada.</p>
                                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                                    Ajuste os filtros ou cadastre uma nova alíquota.
                                                </p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    @if ($aliquotas->hasPages())
                        <div class="mt-5">
                            {{ $aliquotas->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        @include('pages.aliquotas-imposto.partials.create-modal', ['formatPercentual' => $formatPercentual])
        @include('pages.aliquotas-imposto.partials.edit-modal')
    </div>
@endsection
