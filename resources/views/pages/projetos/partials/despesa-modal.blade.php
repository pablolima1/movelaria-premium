@php
    $reopen = old('_modal') === 'despesa';
    $defaults = ['id' => null, 'valor' => '', 'categoria' => 'material', 'descricao' => '', 'data_despesa' => now()->toDateString(), 'status' => 'pago', 'data_pagamento' => '', 'forma_pagamento' => ''];
    $initial = $reopen ? [
        'id' => old('_id'),
        'valor' => \App\Helpers\Formatter::moneyInput(old('valor')),
        'categoria' => old('categoria', 'material'),
        'descricao' => old('descricao') ?? '',
        'data_despesa' => old('data_despesa', ''),
        'status' => old('status', 'pago'),
        'data_pagamento' => old('data_pagamento') ?? '',
        'forma_pagamento' => old('forma_pagamento') ?? '',
    ] : $defaults;
    $baseUrl = url('projetos/' . $projeto->id . '/despesas');
@endphp

<x-ui.modal :isOpen="false" class="max-w-[600px]">
    <div
        x-data="{
            form: @js($initial),
            defaults: @js($defaults),
            get editing() { return !!this.form.id },
        }"
        @if ($reopen) x-init="$nextTick(() => open = true)" @endif
        @open-create-despesa.window="form = { ...defaults }; open = true"
        @open-edit-despesa.window="form = { ...$event.detail }; open = true"
        class="relative w-full max-w-[600px] overflow-y-auto rounded-3xl bg-white p-4 dark:bg-gray-900 lg:p-11"
    >
        <div class="px-2 pr-14">
            <h4 class="mb-2 text-2xl font-semibold text-gray-800 dark:text-white/90" x-text="editing ? 'Editar despesa' : 'Nova despesa'"></h4>
            <p class="mb-6 text-sm text-gray-500 dark:text-gray-400 lg:mb-7">{{ $projeto->nome }}</p>
        </div>

        <form method="POST" :action="editing ? '{{ $baseUrl }}/' + form.id : '{{ $baseUrl }}'" class="flex flex-col">
            @csrf
            <input type="hidden" name="_method" :value="editing ? 'PUT' : 'POST'">
            <input type="hidden" name="_modal" value="despesa">
            <input type="hidden" name="_id" :value="form.id ?? ''">

            <div class="custom-scrollbar overflow-y-auto px-2">
                @include('pages.projetos.partials.despesa-fields', ['modal' => 'despesa'])
            </div>

            @include('pages.projetos.partials.modal-actions', ['label' => 'Salvar despesa'])
        </form>
    </div>
</x-ui.modal>
