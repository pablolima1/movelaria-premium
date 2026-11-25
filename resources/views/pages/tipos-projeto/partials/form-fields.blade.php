@php
    $isEdit = ($mode ?? 'create') === 'edit';
    $modalError = old('_modal') === $mode;
@endphp

<div class="grid grid-cols-1 gap-y-5">
    <div>
        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Nome <span class="text-error-500">*</span>
        </label>
        <input
            type="text"
            name="nome"
            @if($isEdit) x-model="tipoProjeto.nome" @endif
            value="{{ $isEdit ? '' : old('nome') }}"
            maxlength="255"
            placeholder="Ex.: Cozinha planejada"
            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
            required
        />
        @if($modalError && $errors->has('nome'))
            <p class="mt-1 text-theme-xs text-error-500">{{ $errors->first('nome') }}</p>
        @endif
    </div>

    <div>
        <span class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Situação
        </span>
        <input type="hidden" name="ativo" :value="tipoProjeto.ativo ? 1 : 0">
        <label class="flex w-fit cursor-pointer items-center gap-3 text-sm font-medium text-gray-700 select-none dark:text-gray-400">
            <div class="relative">
                <input type="checkbox" class="sr-only" x-model="tipoProjeto.ativo" />
                <div class="block h-6 w-11 rounded-full"
                    :class="tipoProjeto.ativo ? 'bg-brand-500 dark:bg-brand-500' : 'bg-gray-200 dark:bg-white/10'">
                </div>
                <div :class="tipoProjeto.ativo ? 'translate-x-full' : 'translate-x-0'"
                    class="shadow-theme-sm absolute top-0.5 left-0.5 h-5 w-5 rounded-full bg-white duration-300 ease-linear">
                </div>
            </div>
            <span x-text="tipoProjeto.ativo ? 'Ativo' : 'Inativo'"></span>
        </label>
        <p class="mt-1 text-theme-xs text-gray-500 dark:text-gray-400">
            Tipos inativos não ficam disponíveis para novos projetos.
        </p>
        @if($modalError && $errors->has('ativo'))
            <p class="mt-1 text-theme-xs text-error-500">{{ $errors->first('ativo') }}</p>
        @endif
    </div>
</div>
