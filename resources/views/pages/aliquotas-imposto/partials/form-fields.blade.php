@php
    $modalError = old('_modal') === ($mode ?? 'create');
    $inputClass = 'dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30';
@endphp

<div class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-2">
    <div class="sm:col-span-2">
        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Percentual <span class="text-error-500">*</span>
        </label>
        <div class="relative">
            <input
                type="text"
                name="percentual"
                inputmode="numeric"
                x-model="aliquota.percentual"
                x-money
                placeholder="0,00"
                class="{{ $inputClass }} pr-11"
                required
            />
            <span class="pointer-events-none absolute top-1/2 right-4 -translate-y-1/2 text-sm text-gray-500 dark:text-gray-400">%</span>
        </div>
        @if($modalError && $errors->has('percentual'))
            <p class="mt-1 text-theme-xs text-error-500">{{ $errors->first('percentual') }}</p>
        @endif
    </div>

    <div>
        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Vigente desde <span class="text-error-500">*</span>
        </label>
        <x-form.data name="vigente_desde" model="aliquota.vigente_desde" :required="true" />
        @if($modalError && $errors->has('vigente_desde'))
            <p class="mt-1 text-theme-xs text-error-500">{{ $errors->first('vigente_desde') }}</p>
        @endif
    </div>

    <div>
        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Vigente até
        </label>
        <x-form.data name="vigente_ate" model="aliquota.vigente_ate" min="aliquota.vigente_desde" />
        <p class="mt-1 text-theme-xs text-gray-500 dark:text-gray-400">Deixe em branco para vigência sem data final.</p>
        @if($modalError && $errors->has('vigente_ate'))
            <p class="mt-1 text-theme-xs text-error-500">{{ $errors->first('vigente_ate') }}</p>
        @endif
    </div>
</div>
