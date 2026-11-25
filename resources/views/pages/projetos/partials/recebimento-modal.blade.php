@php
    $reopen = old('_modal') === 'recebimento';
    $defaults = ['id' => null, 'valor' => '', 'data_recebimento' => now()->toDateString(), 'forma_pagamento' => 'pix', 'observacoes' => ''];
    $initial = $reopen ? [
        'id' => old('_id'),
        'valor' => \App\Helpers\Formatter::moneyInput(old('valor')),
        'data_recebimento' => old('data_recebimento', ''),
        'forma_pagamento' => old('forma_pagamento', 'pix'),
        'observacoes' => old('observacoes') ?? '',
    ] : $defaults;
    $baseUrl = url('projetos/' . $projeto->id . '/recebimentos');
@endphp

<x-ui.modal :isOpen="false" class="max-w-[600px]">
    <div
        x-data="{
            form: @js($initial),
            defaults: @js($defaults),
            get editing() { return !!this.form.id },
        }"
        @if ($reopen) x-init="$nextTick(() => open = true)" @endif
        @open-create-recebimento.window="form = { ...defaults }; open = true"
        @open-edit-recebimento.window="form = { ...$event.detail }; open = true"
        class="relative w-full max-w-[600px] overflow-y-auto rounded-3xl bg-white p-4 dark:bg-gray-900 lg:p-11"
    >
        <div class="px-2 pr-14">
            <h4 class="mb-2 text-2xl font-semibold text-gray-800 dark:text-white/90" x-text="editing ? 'Editar recebimento' : 'Novo recebimento'"></h4>
            <p class="mb-6 text-sm text-gray-500 dark:text-gray-400 lg:mb-7">{{ $projeto->nome }}</p>
        </div>

        <form method="POST" :action="editing ? '{{ $baseUrl }}/' + form.id : '{{ $baseUrl }}'" class="flex flex-col">
            @csrf
            <input type="hidden" name="_method" :value="editing ? 'PUT' : 'POST'">
            <input type="hidden" name="_modal" value="recebimento">
            <input type="hidden" name="_id" :value="form.id ?? ''">

            <div class="custom-scrollbar overflow-y-auto px-2">
                @include('pages.projetos.partials.recebimento-fields', ['modal' => 'recebimento', 'saldo' => $projeto->valor_final !== null ? $projeto->financeiro['a_receber'] : null])
            </div>

            @include('pages.projetos.partials.modal-actions', ['label' => 'Salvar recebimento'])
        </form>
    </div>
</x-ui.modal>
