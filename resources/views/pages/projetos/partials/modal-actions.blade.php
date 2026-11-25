<div class="mt-6 flex items-center gap-3 lg:justify-end">
    <button @click="open = false" type="button"
        class="flex w-full justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] sm:w-auto">
        Cancelar
    </button>
    <button type="submit"
        class="flex w-full justify-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 sm:w-auto">
        {{ $label ?? 'Salvar' }}
    </button>
</div>
