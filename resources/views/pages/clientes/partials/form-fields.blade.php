@php
    $isEdit = ($mode ?? 'create') === 'edit';
    $modalError = old('_modal') === $mode;
    $fieldValue = function (string $field) use ($isEdit, $modalError) {
        if ($modalError) {
            return old($field);
        }
        return $isEdit ? null : old($field);
    };
@endphp

<div class="grid grid-cols-1 gap-x-6 gap-y-5 lg:grid-cols-2">
    <div class="lg:col-span-2">
        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Nome <span class="text-error-500">*</span>
        </label>
        <input
            type="text"
            name="nome"
            @if($isEdit) x-model="cliente.nome" @endif
            value="{{ $isEdit ? '' : old('nome') }}"
            placeholder="Nome completo ou razão social"
            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
            required
        />
        @if($modalError && $errors->has('nome'))
            <p class="mt-1 text-theme-xs text-error-500">{{ $errors->first('nome') }}</p>
        @endif
    </div>

    <div>
        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            CPF/CNPJ
        </label>
        <input
            type="text"
            name="cpf_cnpj"
            @if($isEdit) x-model="cliente.cpf_cnpj" @endif
            x-mask:dynamic="maskCpfCnpj"
            value="{{ $isEdit ? '' : old('cpf_cnpj') }}"
            placeholder="CPF ou CNPJ"
            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
        />
        @if($modalError && $errors->has('cpf_cnpj'))
            <p class="mt-1 text-theme-xs text-error-500">{{ $errors->first('cpf_cnpj') }}</p>
        @endif
    </div>

    <div>
        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Telefone
        </label>
        <input
            type="text"
            name="telefone"
            @if($isEdit) x-model="cliente.telefone" @endif
            x-mask:dynamic="maskPhone"
            value="{{ $isEdit ? '' : old('telefone') }}"
            placeholder="(00) 00000-0000"
            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
        />
        @if($modalError && $errors->has('telefone'))
            <p class="mt-1 text-theme-xs text-error-500">{{ $errors->first('telefone') }}</p>
        @endif
    </div>

    <div class="lg:col-span-2">
        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            E-mail
        </label>
        <input
            type="email"
            name="email"
            @if($isEdit) x-model="cliente.email" @endif
            value="{{ $isEdit ? '' : old('email') }}"
            placeholder="cliente@email.com"
            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
        />
        @if($modalError && $errors->has('email'))
            <p class="mt-1 text-theme-xs text-error-500">{{ $errors->first('email') }}</p>
        @endif
    </div>

    <div class="lg:col-span-2">
        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Endereço
        </label>
        <textarea
            name="endereco"
            rows="3"
            @if($isEdit) x-model="cliente.endereco" @endif
            placeholder="Rua, número, bairro, cidade - UF"
            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
        >{{ $isEdit ? '' : old('endereco') }}</textarea>
        @if($modalError && $errors->has('endereco'))
            <p class="mt-1 text-theme-xs text-error-500">{{ $errors->first('endereco') }}</p>
        @endif
    </div>

    <div class="lg:col-span-2">
        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Observações
        </label>
        <textarea
            name="observacoes"
            rows="3"
            @if($isEdit) x-model="cliente.observacoes" @endif
            placeholder="Informações adicionais sobre o cliente"
            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
        >{{ $isEdit ? '' : old('observacoes') }}</textarea>
        @if($modalError && $errors->has('observacoes'))
            <p class="mt-1 text-theme-xs text-error-500">{{ $errors->first('observacoes') }}</p>
        @endif
    </div>
</div>
