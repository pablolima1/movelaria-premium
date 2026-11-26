{{-- Campos do projeto. Requer um objeto Alpine `form` no escopo e $modal (chave do modal). --}}
@php
    $inputClass = 'dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30';
    $labelClass = 'mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400';
@endphp

<div class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-2">
    <div class="sm:col-span-2">
        <label class="{{ $labelClass }}">Nome do projeto <span class="text-error-500">*</span></label>
        <input type="text" name="nome" x-model="form.nome" maxlength="255" placeholder="Ex.: Cozinha planejada apto 302" class="{{ $inputClass }}" required />
        @include('pages.projetos.partials.field-error', ['field' => 'nome'])
    </div>

    <div class="sm:col-span-2">
        <label class="{{ $labelClass }}">Cliente <span class="text-error-500">*</span></label>
        <select name="cliente_id" x-model="form.cliente_id" class="{{ $inputClass }}" required>
            <option value="">Selecione o cliente</option>
            @foreach ($clientes as $id => $nome)
                <option value="{{ $id }}">{{ $nome }}</option>
            @endforeach
        </select>
        @if ($clientes->isEmpty())
            <p class="mt-1 text-theme-xs text-warning-600 dark:text-orange-400">
                Nenhum cliente cadastrado. <a href="{{ route('clientes.index') }}" class="font-medium underline">Cadastre um cliente</a> primeiro.
            </p>
        @endif
        @include('pages.projetos.partials.field-error', ['field' => 'cliente_id'])
    </div>

    <div>
        <label class="{{ $labelClass }}">Tipo de projeto <span class="text-error-500">*</span></label>
        <select name="tipo_projeto_id" x-model="form.tipo_projeto_id" class="{{ $inputClass }}" required>
            <option value="">Selecione</option>
            @foreach ($tipos as $tipo)
                @continue(! $tipo->ativo && $tipo->id !== ($tipoAtualId ?? null))
                <option value="{{ $tipo->id }}">{{ $tipo->nome }}{{ $tipo->ativo ? '' : ' (inativo)' }}</option>
            @endforeach
        </select>
        @include('pages.projetos.partials.field-error', ['field' => 'tipo_projeto_id'])
    </div>

    <div>
        <label class="{{ $labelClass }}">Situação <span class="text-error-500">*</span></label>
        <select name="status" x-model="form.status" class="{{ $inputClass }}" required>
            @foreach (\App\Enums\StatusProjeto::options() as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
        </select>
        @include('pages.projetos.partials.field-error', ['field' => 'status'])
    </div>

    <div class="sm:col-span-2">
        <label class="{{ $labelClass }}">Valor total do projeto</label>
        <div class="relative">
            <span class="pointer-events-none absolute top-1/2 left-4 -translate-y-1/2 text-sm text-gray-500 dark:text-gray-400">R$</span>
            <input type="text" name="valor_final" inputmode="numeric" x-model="form.valor_final"
                x-money placeholder="0,00" class="{{ $inputClass }} pl-11" />
        </div>
        <p class="mt-1 text-theme-xs text-gray-500 dark:text-gray-400">O imposto é calculado sobre este valor.</p>
        @include('pages.projetos.partials.field-error', ['field' => 'valor_final'])
    </div>

    {{-- Forma de pagamento do cliente --}}
    <div class="sm:col-span-2">
        <label class="{{ $labelClass }}">Pagamento do cliente <span class="text-error-500">*</span></label>
        <input type="hidden" name="condicao_pagamento" :value="form.condicao_pagamento">
        <div class="grid grid-cols-2 gap-2 rounded-xl bg-gray-100 p-1 dark:bg-white/[0.04]">
            @foreach (\App\Enums\CondicaoPagamento::options() as $value => $label)
                <button type="button" @click="form.condicao_pagamento = '{{ $value }}'"
                    class="rounded-lg py-2.5 text-sm font-medium transition"
                    :class="form.condicao_pagamento === '{{ $value }}' ? 'bg-white text-brand-600 shadow-theme-xs dark:bg-gray-800 dark:text-brand-400' : 'text-gray-500 dark:text-gray-400'">
                    {{ $label }}
                </button>
            @endforeach
        </div>
        @include('pages.projetos.partials.field-error', ['field' => 'condicao_pagamento'])
    </div>

    <template x-if="form.condicao_pagamento === 'parcelado'">
        <div class="sm:col-span-2">
            <label class="{{ $labelClass }}">Número de parcelas <span class="text-error-500">*</span></label>
            <select name="numero_parcelas" x-model="form.numero_parcelas" class="{{ $inputClass }}" required>
                <option value="">Selecione</option>
                @for ($n = 2; $n <= \App\Enums\CondicaoPagamento::MAX_PARCELAS; $n++)
                    <option value="{{ $n }}">{{ $n }}x</option>
                @endfor
            </select>
            <p class="mt-1 text-theme-xs text-gray-500 dark:text-gray-400"
                x-show="form.numero_parcelas && form.valor_final"
                x-text="(() => {
                    const total = parseFloat(String(form.valor_final).replace(/\./g, '').replace(',', '.')) || 0;
                    const parcela = total / Number(form.numero_parcelas);
                    return form.numero_parcelas + 'x de ' + parcela.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
                })()"></p>
            @include('pages.projetos.partials.field-error', ['field' => 'numero_parcelas'])
        </div>
    </template>

    <template x-if="form.condicao_pagamento === 'parcelado'">
        <div class="sm:col-span-2">
            <label class="{{ $labelClass }}">Vencimento da 1ª parcela <span class="text-error-500">*</span></label>
            <x-form.data name="primeiro_vencimento" model="form.primeiro_vencimento" :required="true" />
            <p class="mt-1 text-theme-xs text-gray-500 dark:text-gray-400">As demais vencem no mesmo dia dos meses seguintes. Dá para remarcar cada uma depois.</p>
            @include('pages.projetos.partials.field-error', ['field' => 'primeiro_vencimento'])
        </div>
    </template>

    <div>
        <label class="{{ $labelClass }}">Data de início</label>
        <x-form.data name="data_inicio" model="form.data_inicio" />
        <p class="mt-1 text-theme-xs text-gray-500 dark:text-gray-400">Define a alíquota de imposto aplicada.</p>
        @include('pages.projetos.partials.field-error', ['field' => 'data_inicio'])
    </div>

    <div>
        <label class="{{ $labelClass }}">Data de conclusão</label>
        <x-form.data name="data_conclusao" model="form.data_conclusao" min="form.data_inicio" />
        @include('pages.projetos.partials.field-error', ['field' => 'data_conclusao'])
    </div>

    <div class="sm:col-span-2">
        <label class="{{ $labelClass }}">Observações</label>
        <textarea name="observacoes" rows="3" x-model="form.observacoes" placeholder="Detalhes do projeto, medidas, acabamentos..."
            class="{{ str_replace('h-11 ', '', $inputClass) }}"></textarea>
        @include('pages.projetos.partials.field-error', ['field' => 'observacoes'])
    </div>
</div>
