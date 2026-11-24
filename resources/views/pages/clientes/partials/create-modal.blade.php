<x-ui.modal :isOpen="false" class="max-w-[700px]">
    <div
        x-data="{
            maskCpfCnpj(value) {
                return value.replace(/\D/g, '').length <= 11
                    ? '999.999.999-99'
                    : '99.999.999/9999-99';
            },
            maskPhone(value) {
                return value.replace(/\D/g, '').length <= 10
                    ? '(99) 9999-9999'
                    : '(99) 99999-9999';
            }
        }"
        @open-create-cliente.window="open = true"
        class="relative w-full max-w-[700px] overflow-y-auto rounded-3xl bg-white p-4 dark:bg-gray-900 lg:p-11"
    >
        <div class="px-2 pr-14">
            <h4 class="mb-2 text-2xl font-semibold text-gray-800 dark:text-white/90">
                Cadastrar cliente
            </h4>
            <p class="mb-6 text-sm text-gray-500 dark:text-gray-400 lg:mb-7">
                Preencha os dados para cadastrar um novo cliente.
            </p>
        </div>

        <form method="POST" action="{{ route('clientes.store') }}" class="flex flex-col">
            @csrf
            <input type="hidden" name="_modal" value="create">
            <input type="hidden" name="q" value="{{ $filters['q'] ?? '' }}">

            <div class="custom-scrollbar overflow-y-auto px-2">
                @include('pages.clientes.partials.form-fields', ['mode' => 'create'])
            </div>

            <div class="mt-6 flex items-center gap-3 lg:justify-end">
                <button @click="open = false" type="button"
                    class="flex w-full justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] sm:w-auto">
                    Cancelar
                </button>
                <button type="submit"
                    class="flex w-full justify-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 sm:w-auto">
                    Salvar cliente
                </button>
            </div>
        </form>
    </div>
</x-ui.modal>
