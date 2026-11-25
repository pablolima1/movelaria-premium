<x-ui.modal :isOpen="false" class="max-w-[600px]">
    <div
        x-data="{
            aliquota: { id: null, percentual: '', vigente_desde: '', vigente_ate: '' },
            updateUrl(id) {
                return '{{ url('aliquotas-imposto') }}/' + id;
            }
        }"
        @open-edit-aliquota.window="open = true; aliquota = $event.detail"
        class="relative w-full max-w-[600px] overflow-y-auto rounded-3xl bg-white p-4 dark:bg-gray-900 lg:p-11"
    >
        <div class="px-2 pr-14">
            <h4 class="mb-2 text-2xl font-semibold text-gray-800 dark:text-white/90">
                Editar alíquota
            </h4>
            <p class="mb-6 text-sm text-gray-500 dark:text-gray-400 lg:mb-7">
                Atualize o percentual ou o período de vigência da alíquota selecionada.
            </p>
        </div>

        <form method="POST" :action="updateUrl(aliquota.id)" class="flex flex-col">
            @csrf
            @method('PUT')
            <input type="hidden" name="_modal" value="edit">
            <input type="hidden" name="_aliquota_id" :value="aliquota.id">
            <input type="hidden" name="status" value="{{ $filters['status'] ?? '' }}">

            <div class="custom-scrollbar overflow-y-auto px-2">
                @include('pages.aliquotas-imposto.partials.form-fields', ['mode' => 'edit'])
            </div>

            <div class="mt-6 flex items-center gap-3 lg:justify-end">
                <button @click="open = false" type="button"
                    class="flex w-full justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] sm:w-auto">
                    Cancelar
                </button>
                <button type="submit"
                    class="flex w-full justify-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 sm:w-auto">
                    Salvar alterações
                </button>
            </div>
        </form>
    </div>
</x-ui.modal>
