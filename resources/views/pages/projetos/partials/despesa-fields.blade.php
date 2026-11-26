{{-- Requer objeto Alpine `form` e $modal. --}}
@php
    $inputClass = 'dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30';
    $labelClass = 'mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400';
@endphp

<div class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-2">
    <div class="sm:col-span-2">
        <label class="{{ $labelClass }}">Valor <span class="text-error-500">*</span></label>
        <div class="relative">
            <span class="pointer-events-none absolute top-1/2 left-4 -translate-y-1/2 text-sm text-gray-500 dark:text-gray-400">R$</span>
            <input type="text" name="valor" inputmode="numeric" x-model="form.valor"
                x-money placeholder="0,00" class="{{ $inputClass }} pl-11 text-base font-semibold" required />
        </div>
        @include('pages.projetos.partials.field-error', ['field' => 'valor'])
    </div>

    <div>
        <label class="{{ $labelClass }}">Categoria <span class="text-error-500">*</span></label>
        <select name="categoria" x-model="form.categoria" class="{{ $inputClass }}" required>
            @foreach (\App\Enums\CategoriaDespesa::options() as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
        </select>
        @include('pages.projetos.partials.field-error', ['field' => 'categoria'])
    </div>

    <div>
        <label class="{{ $labelClass }}">Data <span class="text-error-500">*</span></label>
        <x-form.data name="data_despesa" model="form.data_despesa" :required="true" />
        @include('pages.projetos.partials.field-error', ['field' => 'data_despesa'])
    </div>

    <div class="sm:col-span-2">
        <label class="{{ $labelClass }}">Descrição</label>
        <input type="text" name="descricao" x-model="form.descricao" maxlength="255" placeholder="Ex.: MDF, ferragens, fita de borda..." class="{{ $inputClass }}" />
        @include('pages.projetos.partials.field-error', ['field' => 'descricao'])
    </div>

    <div class="sm:col-span-2">
        <input type="hidden" name="status" :value="form.status">
        <label class="flex w-fit cursor-pointer items-center gap-3 text-sm font-medium text-gray-700 select-none dark:text-gray-400">
            <div class="relative">
                <input type="checkbox" class="sr-only" :checked="form.status === 'pago'" @change="form.status = $event.target.checked ? 'pago' : 'pendente'" />
                <div class="block h-6 w-11 rounded-full" :class="form.status === 'pago' ? 'bg-brand-500 dark:bg-brand-500' : 'bg-gray-200 dark:bg-white/10'"></div>
                <div :class="form.status === 'pago' ? 'translate-x-full' : 'translate-x-0'" class="shadow-theme-sm absolute top-0.5 left-0.5 h-5 w-5 rounded-full bg-white duration-300 ease-linear"></div>
            </div>
            <span x-text="form.status === 'pago' ? 'Já está paga' : 'Pagamento pendente'"></span>
        </label>
        @include('pages.projetos.partials.field-error', ['field' => 'status'])
    </div>

    <template x-if="form.status === 'pago'">
        <div class="contents">
            <div>
                <label class="{{ $labelClass }}">Pago em</label>
                <x-form.data name="data_pagamento" model="form.data_pagamento" />
                <p class="mt-1 text-theme-xs text-gray-500 dark:text-gray-400">Em branco: usa a data da despesa.</p>
                @include('pages.projetos.partials.field-error', ['field' => 'data_pagamento'])
            </div>
            <div>
                <label class="{{ $labelClass }}">Forma de pagamento</label>
                <select name="forma_pagamento" x-model="form.forma_pagamento" class="{{ $inputClass }}">
                    <option value="">Não informar</option>
                    @foreach (\App\Enums\FormaPagamento::options() as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
                @include('pages.projetos.partials.field-error', ['field' => 'forma_pagamento'])
            </div>
        </div>
    </template>
</div>
