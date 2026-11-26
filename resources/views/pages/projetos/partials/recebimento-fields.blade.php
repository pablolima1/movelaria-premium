{{-- Requer objeto Alpine `form` e $modal. Opcional: $saldo (a receber). --}}
@php
    $inputClass = 'dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30';
    $labelClass = 'mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400';
@endphp

<div class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-2">
    <div class="sm:col-span-2">
        <label class="{{ $labelClass }}">Valor recebido <span class="text-error-500">*</span></label>
        <div class="relative">
            <span class="pointer-events-none absolute top-1/2 left-4 -translate-y-1/2 text-sm text-gray-500 dark:text-gray-400">R$</span>
            <input type="text" name="valor" inputmode="numeric" x-model="form.valor"
                x-money placeholder="0,00" class="{{ $inputClass }} pl-11 text-base font-semibold" required />
        </div>
        @isset($saldo)
            @if (! is_null($saldo))
                <p class="mt-1 text-theme-xs text-gray-500 dark:text-gray-400">
                    Saldo a receber: <button type="button" class="font-medium text-brand-500 hover:text-brand-600"
                        @click="form.valor = @js(\App\Helpers\Formatter::moneyInput($saldo))">{{ \App\Helpers\Formatter::money($saldo) }}</button>
                </p>
            @endif
        @endisset
        @include('pages.projetos.partials.field-error', ['field' => 'valor'])
    </div>

    <div>
        <label class="{{ $labelClass }}">Data <span class="text-error-500">*</span></label>
        <x-form.data name="data_recebimento" model="form.data_recebimento" :required="true" />
        @include('pages.projetos.partials.field-error', ['field' => 'data_recebimento'])
    </div>

    <div>
        <label class="{{ $labelClass }}">Forma de pagamento <span class="text-error-500">*</span></label>
        <select name="forma_pagamento" x-model="form.forma_pagamento" class="{{ $inputClass }}" required>
            @foreach (\App\Enums\FormaPagamento::options() as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
        </select>
        @include('pages.projetos.partials.field-error', ['field' => 'forma_pagamento'])
    </div>

    <div class="sm:col-span-2">
        <label class="{{ $labelClass }}">Observações</label>
        <input type="text" name="observacoes" x-model="form.observacoes" placeholder="Ex.: entrada, 2ª parcela..." class="{{ $inputClass }}" />
        @include('pages.projetos.partials.field-error', ['field' => 'observacoes'])
    </div>
</div>
