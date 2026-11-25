@php
    $reopen = old('_modal') === 'projeto-create';
    $initial = $reopen ? [
        'nome' => old('nome', ''),
        'cliente_id' => (string) old('cliente_id', ''),
        'tipo_projeto_id' => (string) old('tipo_projeto_id', ''),
        'status' => old('status', 'agendado'),
        'valor_final' => \App\Helpers\Formatter::moneyInput(old('valor_final')),
        'condicao_pagamento' => old('condicao_pagamento', 'a_vista'),
        'numero_parcelas' => (string) old('numero_parcelas', ''),
        'primeiro_vencimento' => old('primeiro_vencimento') ?? '',
        'data_inicio' => old('data_inicio') ?? '',
        'data_conclusao' => old('data_conclusao') ?? '',
        'observacoes' => old('observacoes') ?? '',
    ] : [
        'nome' => '', 'cliente_id' => '', 'tipo_projeto_id' => '', 'status' => 'agendado', 'valor_final' => '',
        'condicao_pagamento' => 'a_vista', 'numero_parcelas' => '', 'primeiro_vencimento' => now()->toDateString(),
        'data_inicio' => now()->toDateString(), 'data_conclusao' => '', 'observacoes' => '',
    ];
@endphp

<x-ui.modal :isOpen="false" class="max-w-[700px]">
    <div
        x-data="{ form: @js($initial) }"
        @if ($reopen) x-init="$nextTick(() => open = true)" @endif
        @open-create-projeto.window="open = true"
        class="relative flex max-h-[calc(100dvh-2.5rem)] w-full max-w-[700px] flex-col overflow-hidden rounded-3xl bg-white p-4 dark:bg-gray-900 lg:p-11"
    >
        <div class="px-2 pr-14">
            <h4 class="mb-2 text-2xl font-semibold text-gray-800 dark:text-white/90">Novo projeto</h4>
            <p class="mb-6 text-sm text-gray-500 dark:text-gray-400 lg:mb-7">
                Cadastre o projeto. Depois você lança os recebimentos e as despesas dele.
            </p>
        </div>

        <form method="POST" action="{{ route('projetos.store') }}" class="flex min-h-0 flex-1 flex-col">
            @csrf
            <input type="hidden" name="_modal" value="projeto-create">

            <div class="custom-scrollbar min-h-0 flex-1 overflow-y-auto px-2 pb-1">
                @include('pages.projetos.partials.form-fields', ['modal' => 'projeto-create'])
            </div>

            @include('pages.projetos.partials.modal-actions', ['label' => 'Salvar projeto'])
        </form>
    </div>
</x-ui.modal>
