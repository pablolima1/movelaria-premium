{{-- Modais "Receber parcela" e "Remarcar vencimento" (disparados por parcela-item). --}}
@php
    $hoje = now()->toDateString();
    $inputClass = 'dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90';
    $labelClass = 'mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400';

    $reabrirReceber = old('_modal') === 'receber-parcela';
    $receber = [
        'id' => $reabrirReceber ? old('_id') : null,
        'descricao' => $reabrirReceber ? old('_descricao', '') : '',
        'valor' => $reabrirReceber ? old('_valor', '') : '',
        'data_recebimento' => $reabrirReceber ? old('data_recebimento', $hoje) : $hoje,
        'forma_pagamento' => $reabrirReceber ? old('forma_pagamento', 'pix') : 'pix',
    ];

    $reabrirRemarcar = old('_modal') === 'remarcar-parcela';
    $remarcar = [
        'id' => $reabrirRemarcar ? old('_id') : null,
        'descricao' => $reabrirRemarcar ? old('_descricao', '') : '',
        'vencimento' => $reabrirRemarcar ? old('vencimento', '') : '',
    ];
@endphp

<x-ui.modal :isOpen="false" class="max-w-[520px]">
    <div
        x-data="{ form: @js($receber) }"
        @if ($reabrirReceber) x-init="$nextTick(() => open = true)" @endif
        @open-receber-parcela.window="form = { ...form, ...$event.detail, data_recebimento: @js($hoje) }; open = true"
        class="relative w-full overflow-y-auto rounded-3xl bg-white p-4 dark:bg-gray-900 lg:p-10"
    >
        <div class="px-2 pr-14">
            <h4 class="mb-1 text-2xl font-semibold text-gray-800 dark:text-white/90">Receber parcela</h4>
            <p class="text-sm text-gray-500 dark:text-gray-400" x-text="form.descricao"></p>
            <p class="mt-3 mb-6 text-2xl font-bold text-gray-800 dark:text-white/90" x-text="form.valor"></p>
        </div>

        <form method="POST" :action="'{{ url('parcelas') }}/' + form.id + '/receber'" class="flex flex-col">
            @csrf
            <input type="hidden" name="_modal" value="receber-parcela">
            <input type="hidden" name="_id" :value="form.id">
            <input type="hidden" name="_descricao" :value="form.descricao">
            <input type="hidden" name="_valor" :value="form.valor">

            <div class="space-y-5 px-2">
                @if ($reabrirReceber && $errors->has('parcela'))
                    <p class="rounded-lg bg-error-50 p-3 text-sm text-error-600 dark:bg-error-500/15 dark:text-error-500">{{ $errors->first('parcela') }}</p>
                @endif
                <div>
                    <label class="{{ $labelClass }}">Data do recebimento <span class="text-error-500">*</span></label>
                    <x-form.data name="data_recebimento" model="form.data_recebimento" :required="true" />
                    @include('pages.projetos.partials.field-error', ['modal' => 'receber-parcela', 'field' => 'data_recebimento'])
                </div>
                <div>
                    <label class="{{ $labelClass }}">Forma de pagamento <span class="text-error-500">*</span></label>
                    <select name="forma_pagamento" x-model="form.forma_pagamento" class="{{ $inputClass }}" required>
                        @foreach (\App\Enums\FormaPagamento::options() as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @include('pages.projetos.partials.field-error', ['modal' => 'receber-parcela', 'field' => 'forma_pagamento'])
                </div>
                <p class="text-theme-xs text-gray-500 dark:text-gray-400">O valor entra nos recebimentos do projeto. Para desfazer, exclua o recebimento no projeto.</p>
            </div>

            @include('pages.projetos.partials.modal-actions', ['label' => 'Confirmar recebimento'])
        </form>
    </div>
</x-ui.modal>

<x-ui.modal :isOpen="false" class="max-w-[520px]">
    <div
        x-data="{ form: @js($remarcar) }"
        @if ($reabrirRemarcar) x-init="$nextTick(() => open = true)" @endif
        @open-remarcar-parcela.window="form = { ...$event.detail }; open = true"
        class="relative w-full overflow-y-auto rounded-3xl bg-white p-4 dark:bg-gray-900 lg:p-10"
    >
        <div class="px-2 pr-14">
            <h4 class="mb-1 text-2xl font-semibold text-gray-800 dark:text-white/90">Remarcar vencimento</h4>
            <p class="mb-6 text-sm text-gray-500 dark:text-gray-400" x-text="form.descricao"></p>
        </div>

        <form method="POST" :action="'{{ url('parcelas') }}/' + form.id" class="flex flex-col">
            @csrf
            @method('PATCH')
            <input type="hidden" name="_modal" value="remarcar-parcela">
            <input type="hidden" name="_id" :value="form.id">
            <input type="hidden" name="_descricao" :value="form.descricao">

            <div class="px-2">
                <label class="{{ $labelClass }}">Novo vencimento <span class="text-error-500">*</span></label>
                <x-form.data name="vencimento" model="form.vencimento" :required="true" />
                @include('pages.projetos.partials.field-error', ['modal' => 'remarcar-parcela', 'field' => 'vencimento'])
                <p class="mt-1 text-theme-xs text-gray-500 dark:text-gray-400">Só esta parcela é alterada.</p>
            </div>

            @include('pages.projetos.partials.modal-actions', ['label' => 'Salvar'])
        </form>
    </div>
</x-ui.modal>
