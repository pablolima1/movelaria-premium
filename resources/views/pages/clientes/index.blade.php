@extends('layouts.app')

@section('content')
    @php
        $createModalOpen = $errors->any() && old('_modal') === 'create';
        $editModalOpen = $errors->any() && old('_modal') === 'edit';
        $editOldCliente = $editModalOpen ? [
            'id' => old('_cliente_id'),
            'nome' => old('nome', ''),
            'cpf_cnpj' => old('cpf_cnpj', ''),
            'telefone' => old('telefone', ''),
            'email' => old('email', ''),
            'endereco' => old('endereco', ''),
            'observacoes' => old('observacoes', ''),
        ] : null;
    @endphp

    <div
        x-data="{
            init() {
                @if($createModalOpen)
                    this.$dispatch('open-create-cliente');
                @endif
                @if($editModalOpen && $editOldCliente && $editOldCliente['id'])
                    this.$dispatch('open-edit-cliente', @js($editOldCliente));
                @endif
            }
        }"
    >
        <x-common.page-breadcrumb pageTitle="Clientes" />

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

            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="flex flex-col gap-4 border-b border-gray-100 px-6 py-5 dark:border-gray-800 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <h3 class="text-base font-medium text-gray-800 dark:text-white/90">Listagem de clientes</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Pesquise e gerencie os clientes cadastrados.
                        </p>
                    </div>

                    <button
                        type="button"
                        @click="$dispatch('open-create-cliente')"
                        class="inline-flex items-center justify-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600"
                    >
                        <svg class="fill-current" width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M10 4.1665C10.4603 4.1665 10.8334 4.5396 10.8334 4.99984V9.1665H15C15.4603 9.1665 15.8334 9.5396 15.8334 9.99984C15.8334 10.4601 15.4603 10.8332 15 10.8332H10.8334V14.9998C10.8334 15.4601 10.4603 15.8332 10 15.8332C9.53978 15.8332 9.16669 15.4601 9.16669 14.9998V10.8332H5.00002C4.53978 10.8332 4.16669 10.4601 4.16669 9.99984C4.16669 9.5396 4.53978 9.1665 5.00002 9.1665H9.16669V4.99984C9.16669 4.5396 9.53978 4.1665 10 4.1665Z" fill="" />
                        </svg>
                        Novo cliente
                    </button>
                </div>

                <div class="border-b border-gray-100 p-4 dark:border-gray-800 sm:p-6">
                    <form method="GET" action="{{ route('clientes.index') }}" class="grid grid-cols-1 gap-4 md:grid-cols-12 md:items-end">
                        <div class="md:col-span-9">
                            <label for="q" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                Pesquisa
                            </label>
                            <input
                                id="q"
                                type="text"
                                name="q"
                                value="{{ $filters['q'] ?? '' }}"
                                placeholder="Buscar por nome, CPF/CNPJ, e-mail ou telefone"
                                class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                            />
                        </div>

                        <div class="flex flex-col gap-3 sm:flex-row md:col-span-3">
                            <button type="submit"
                                class="inline-flex h-11 w-full items-center justify-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600 sm:w-auto">
                                Pesquisar
                            </button>
                            @if(($filters['q'] ?? '') !== '')
                                <a href="{{ route('clientes.index') }}"
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
                            <table class="w-full min-w-[980px]">
                                <thead>
                                    <tr class="border-b border-gray-100 bg-gray-50 dark:border-gray-800 dark:bg-gray-900/40">
                                        <th class="px-5 py-3 text-left sm:px-6">
                                            <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Cliente</p>
                                        </th>
                                        <th class="px-5 py-3 text-left sm:px-6">
                                            <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">CPF/CNPJ</p>
                                        </th>
                                        <th class="px-5 py-3 text-left sm:px-6">
                                            <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Contato</p>
                                        </th>
                                        <th class="px-5 py-3 text-left sm:px-6">
                                            <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Endereço</p>
                                        </th>
                                        <th class="px-5 py-3 text-right sm:px-6">
                                            <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Ações</p>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($clientes as $cliente)
                                        <tr class="border-b border-gray-100 last:border-b-0 dark:border-gray-800">
                                            <td class="px-5 py-4 sm:px-6">
                                                <p class="font-medium text-gray-800 text-theme-sm dark:text-white/90">{{ $cliente->nome }}</p>
                                                @if ($cliente->observacoes)
                                                    <p class="mt-0.5 max-w-md truncate text-gray-500 text-theme-xs dark:text-gray-400" title="{{ $cliente->observacoes }}">
                                                        {{ $cliente->observacoes }}
                                                    </p>
                                                @endif
                                            </td>
                                            <td class="px-5 py-4 sm:px-6">
                                                <p class="text-gray-600 text-theme-sm dark:text-gray-300">{{ $cliente->cpf_cnpj ?: '—' }}</p>
                                            </td>
                                            <td class="px-5 py-4 sm:px-6">
                                                <p class="text-gray-600 text-theme-sm dark:text-gray-300">{{ $cliente->email ?: '—' }}</p>
                                                <p class="text-gray-500 text-theme-xs dark:text-gray-400">{{ $cliente->telefone ?: '—' }}</p>
                                            </td>
                                            <td class="px-5 py-4 sm:px-6">
                                                <p class="max-w-sm truncate text-gray-600 text-theme-sm dark:text-gray-300" title="{{ $cliente->endereco }}">
                                                    {{ $cliente->endereco ?: '—' }}
                                                </p>
                                            </td>
                                            <td class="px-5 py-4 sm:px-6">
                                                <div class="flex items-center justify-end gap-2">
                                                    <button
                                                        type="button"
                                                        @click="$dispatch('open-edit-cliente', @js([
                                                            'id' => $cliente->id,
                                                            'nome' => $cliente->nome,
                                                            'cpf_cnpj' => $cliente->cpf_cnpj ?? '',
                                                            'telefone' => $cliente->telefone ?? '',
                                                            'email' => $cliente->email ?? '',
                                                            'endereco' => $cliente->endereco ?? '',
                                                            'observacoes' => $cliente->observacoes ?? '',
                                                        ]))"
                                                        class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-300 bg-white text-gray-600 transition hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200"
                                                        title="Editar cliente"
                                                    >
                                                        <svg class="fill-current" width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M15.0911 2.78206C14.2125 1.90338 12.7878 1.90338 11.9092 2.78206L4.57524 10.116C4.26682 10.4244 4.0547 10.8158 3.96468 11.2426L3.31231 14.3352C3.25997 14.5833 3.33653 14.841 3.51583 15.0203C3.69512 15.1996 3.95286 15.2761 4.20096 15.2238L7.29355 14.5714C7.72031 14.4814 8.11172 14.2693 8.42013 13.9609L15.7541 6.62695C16.6327 5.74827 16.6327 4.32365 15.7541 3.44497L15.0911 2.78206ZM12.9698 3.84272C13.2627 3.54982 13.7376 3.54982 14.0305 3.84272L14.6934 4.50563C14.9863 4.79852 14.9863 5.2734 14.6934 5.56629L14.044 6.21573L12.3204 4.49215L12.9698 3.84272ZM11.2597 5.55281L5.6359 11.1766C5.53309 11.2794 5.46238 11.4099 5.43238 11.5522L5.01758 13.5185L6.98394 13.1037C7.1262 13.0737 7.25666 13.003 7.35947 12.9002L12.9833 7.27639L11.2597 5.55281Z" fill="" />
                                                        </svg>
                                                    </button>

                                                    <form method="POST" action="{{ route('clientes.destroy', $cliente) }}" onsubmit="return confirm('Tem certeza que deseja excluir este cliente?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <input type="hidden" name="q" value="{{ $filters['q'] ?? '' }}">
                                                        <button
                                                            type="submit"
                                                            class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-red-200 bg-white text-red-600 transition hover:bg-red-50 dark:border-red-500/30 dark:bg-gray-800 dark:text-red-400 dark:hover:bg-red-500/10"
                                                            title="Excluir cliente"
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
                                                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Nenhum cliente encontrado.</p>
                                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                                    Ajuste os filtros ou cadastre um novo cliente.
                                                </p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    @if ($clientes->hasPages())
                        <div class="mt-5">
                            {{ $clientes->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        @include('pages.clientes.partials.create-modal')
        @include('pages.clientes.partials.edit-modal')
    </div>
@endsection
