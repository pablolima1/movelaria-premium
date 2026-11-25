{{-- Lançamento rápido de recebimento/despesa (botão "+ Lançar" da barra inferior). --}}
@php
    $reopen = old('_modal') === 'lancar';
    $hoje = now()->toDateString();
    $initial = [
        'tipo' => $reopen ? old('_tipo', 'recebimento') : 'recebimento',
        'projetoId' => $reopen ? (string) old('_projeto_id', '') : '',
        'form' => [
            'valor' => $reopen ? \App\Helpers\Formatter::moneyInput(old('valor')) : '',
            'data_recebimento' => $reopen ? old('data_recebimento', $hoje) : $hoje,
            'forma_pagamento' => $reopen ? (old('forma_pagamento') ?? '') : 'pix',
            'observacoes' => $reopen ? (old('observacoes') ?? '') : '',
            'categoria' => $reopen ? old('categoria', 'material') : 'material',
            'descricao' => $reopen ? (old('descricao') ?? '') : '',
            'data_despesa' => $reopen ? old('data_despesa', $hoje) : $hoje,
            'status' => $reopen ? old('status', 'pago') : 'pago',
            'data_pagamento' => $reopen ? (old('data_pagamento') ?? '') : '',
        ],
    ];
    $inputClass = 'dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90';
@endphp

<x-ui.modal :isOpen="false" class="max-w-[600px]">
    <div
        x-data="{
            ...@js($initial),
            get action() {
                return '{{ url('projetos') }}/' + this.projetoId + '/' + (this.tipo === 'recebimento' ? 'recebimentos' : 'despesas');
            },
            setTipo(tipo) {
                this.tipo = tipo;
                if (tipo === 'recebimento' && !this.form.forma_pagamento) this.form.forma_pagamento = 'pix';
                if (tipo === 'despesa' && this.form.status === 'pendente') this.form.forma_pagamento = '';
            }
        }"
        @if ($reopen) x-init="$nextTick(() => open = true)" @endif
        @open-lancar.window="open = true"
        class="relative w-full max-w-[600px] overflow-y-auto rounded-3xl bg-white p-4 dark:bg-gray-900 lg:p-11"
    >
        <div class="px-2 pr-14">
            <h4 class="mb-4 text-2xl font-semibold text-gray-800 dark:text-white/90">Novo lançamento</h4>
        </div>

        @if (empty($projetosLancamento))
            <div class="px-2 pb-2">
                <p class="text-sm text-gray-500 dark:text-gray-400">Cadastre um projeto para lançar recebimentos e despesas.</p>
                <a href="{{ route('projetos.index') }}" class="mt-4 inline-flex rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600">Ir para projetos</a>
            </div>
        @else
            <div class="mb-5 grid grid-cols-2 gap-2 rounded-xl bg-gray-100 p-1 mx-2 dark:bg-white/[0.04]">
                <button type="button" @click="setTipo('recebimento')"
                    class="rounded-lg py-2.5 text-sm font-medium transition"
                    :class="tipo === 'recebimento' ? 'bg-white text-success-600 shadow-theme-xs dark:bg-gray-800 dark:text-success-500' : 'text-gray-500 dark:text-gray-400'">
                    ↓ Recebimento
                </button>
                <button type="button" @click="setTipo('despesa')"
                    class="rounded-lg py-2.5 text-sm font-medium transition"
                    :class="tipo === 'despesa' ? 'bg-white text-gray-800 shadow-theme-xs dark:bg-gray-800 dark:text-white/90' : 'text-gray-500 dark:text-gray-400'">
                    ↑ Despesa
                </button>
            </div>

            <form method="POST" :action="action" class="flex flex-col">
                @csrf
                <input type="hidden" name="_modal" value="lancar">
                <input type="hidden" name="_tipo" :value="tipo">
                <input type="hidden" name="_projeto_id" :value="projetoId">

                <div class="custom-scrollbar overflow-y-auto px-2">
                    <div class="mb-5">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Projeto <span class="text-error-500">*</span></label>
                        <select x-model="projetoId" class="{{ $inputClass }}" required>
                            <option value="">Selecione o projeto</option>
                            @foreach ($projetosLancamento as $p)
                                <option value="{{ $p['id'] }}">{{ $p['nome'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <template x-if="tipo === 'recebimento'">
                        <div>@include('pages.projetos.partials.recebimento-fields', ['modal' => 'lancar'])</div>
                    </template>
                    <template x-if="tipo === 'despesa'">
                        <div>@include('pages.projetos.partials.despesa-fields', ['modal' => 'lancar'])</div>
                    </template>
                </div>

                @include('pages.projetos.partials.modal-actions', ['label' => 'Lançar'])
            </form>
        @endif
    </div>
</x-ui.modal>
