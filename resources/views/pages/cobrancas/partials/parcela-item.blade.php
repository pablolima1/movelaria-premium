{{-- Linha de uma parcela. Requer $parcela (com projeto carregado). Opcional: $comProjeto (mostra projeto/cliente). --}}
@use('App\Helpers\Formatter')
@use('App\Enums\FormaPagamento')

@php
    $projetoP = $parcela->projeto;
    $status = $parcela->status();
    $comProjeto = $comProjeto ?? false;
    $descricao = "Parcela {$parcela->numero} de {$projetoP->numero_parcelas}" . ($comProjeto ? " · {$projetoP->nome}" : '');

    $whatsapp = null;
    $telefone = preg_replace('/\D/', '', (string) $projetoP->cliente?->telefone);
    if (! $parcela->isPaga() && strlen($telefone) >= 10) {
        $telefone = strlen($telefone) <= 11 ? '55' . $telefone : $telefone;
        $primeiroNome = strtok((string) $projetoP->cliente->nome, ' ');
        $mensagem = $status === \App\Enums\StatusParcela::Atrasada
            ? "Olá {$primeiroNome}, tudo bem? Consta em aberto a parcela {$parcela->numero}/{$projetoP->numero_parcelas} do projeto \"{$projetoP->nome}\", no valor de " . Formatter::money($parcela->valor) . ', vencida em ' . Formatter::date($parcela->vencimento) . '. Pode verificar para mim, por favor?'
            : "Olá {$primeiroNome}, tudo bem? Passando para lembrar da parcela {$parcela->numero}/{$projetoP->numero_parcelas} do projeto \"{$projetoP->nome}\", no valor de " . Formatter::money($parcela->valor) . ', com vencimento em ' . Formatter::date($parcela->vencimento) . '.';
        $whatsapp = 'https://wa.me/' . $telefone . '?text=' . rawurlencode($mensagem);
    }

    $btn = 'inline-flex h-9 items-center justify-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-white/[0.03]';
@endphp

<li class="flex flex-col gap-3 py-4 sm:flex-row sm:items-center sm:justify-between">
    <div class="min-w-0">
        @if ($comProjeto)
            <a href="{{ route('projetos.show', $projetoP) }}" class="block truncate text-sm font-semibold text-gray-800 hover:text-brand-600 dark:text-white/90 dark:hover:text-brand-400">{{ $projetoP->nome }}</a>
            <p class="truncate text-theme-xs text-gray-500 dark:text-gray-400">{{ $projetoP->cliente?->nome ?? '—' }}</p>
        @endif
        <div class="mt-1 flex flex-wrap items-center gap-x-2 gap-y-1 text-sm">
            <span class="font-medium text-gray-700 dark:text-gray-300">Parcela {{ $parcela->numero }}/{{ $projetoP->numero_parcelas }}</span>
            <span class="text-gray-400">·</span>
            <span class="text-gray-500 dark:text-gray-400">vence {{ Formatter::date($parcela->vencimento) }}</span>
            <span class="rounded-full px-2 py-0.5 text-[11px] font-medium {{ $status->badgeClass() }}">
                {{ $status->label() }}@if ($parcela->diasAtraso() > 0) há {{ $parcela->diasAtraso() }} {{ $parcela->diasAtraso() === 1 ? 'dia' : 'dias' }}@elseif (! $parcela->isPaga() && $parcela->vencimento->isToday()) · vence hoje @endif
            </span>
        </div>
        @if ($parcela->isPaga() && $parcela->recebimento)
            <p class="mt-0.5 text-theme-xs text-gray-500 dark:text-gray-400">
                Recebida em {{ Formatter::date($parcela->recebimento->data_recebimento) }} · {{ FormaPagamento::labelFor($parcela->recebimento->forma_pagamento) }}
            </p>
        @endif
    </div>

    <div class="flex items-center justify-between gap-2 sm:justify-end">
        <span class="text-base font-semibold {{ $status === \App\Enums\StatusParcela::Atrasada ? 'text-error-600 dark:text-error-500' : 'text-gray-800 dark:text-white/90' }} sm:mr-2">
            {{ Formatter::money($parcela->valor) }}
        </span>

        @unless ($parcela->isPaga())
            <div class="flex items-center gap-2">
                @if ($whatsapp)
                    <a href="{{ $whatsapp }}" target="_blank" rel="noopener" class="{{ $btn }} !px-2.5 text-success-600 dark:text-success-500" title="Cobrar pelo WhatsApp">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38a9.9 9.9 0 0 0 4.74 1.21h.01c5.46 0 9.9-4.45 9.9-9.91 0-2.65-1.03-5.14-2.9-7.01A9.82 9.82 0 0 0 12.04 2Zm0 18.15h-.01a8.23 8.23 0 0 1-4.2-1.15l-.3-.18-3.12.82.83-3.04-.2-.31a8.2 8.2 0 0 1-1.26-4.38c0-4.54 3.7-8.23 8.25-8.23a8.2 8.2 0 0 1 5.83 2.42 8.18 8.18 0 0 1 2.41 5.82c0 4.54-3.7 8.23-8.23 8.23Zm4.52-6.16c-.25-.12-1.47-.72-1.69-.81-.23-.08-.39-.12-.56.13-.16.25-.64.81-.79.97-.14.17-.29.19-.54.06-.25-.12-1.05-.39-1.99-1.23-.74-.66-1.23-1.47-1.38-1.72-.14-.25-.02-.38.11-.51.11-.11.25-.29.37-.43.13-.15.17-.25.25-.42.08-.16.04-.31-.02-.43-.06-.12-.56-1.34-.76-1.84-.2-.48-.41-.42-.56-.43h-.48a.92.92 0 0 0-.66.31c-.23.25-.87.85-.87 2.07 0 1.22.89 2.4 1.01 2.56.12.17 1.75 2.67 4.24 3.74.59.26 1.05.41 1.41.52.59.19 1.13.16 1.56.1.48-.07 1.47-.6 1.67-1.18.21-.58.21-1.07.15-1.18-.06-.1-.23-.16-.48-.29Z"/></svg>
                    </a>
                @endif
                <button type="button" class="{{ $btn }} !px-2.5" title="Remarcar vencimento"
                    @click="$dispatch('open-remarcar-parcela', @js(['id' => $parcela->id, 'descricao' => $descricao, 'vencimento' => $parcela->vencimento->format('Y-m-d')]))">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><rect x="3.75" y="5.25" width="16.5" height="15" rx="2.25" stroke="currentColor" stroke-width="1.5"/><path d="M3.75 9.75h16.5M8 3.25v3.5M16 3.25v3.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                </button>
                <button type="button" class="inline-flex h-9 items-center justify-center rounded-lg bg-brand-500 px-3.5 text-sm font-medium text-white transition hover:bg-brand-600"
                    @click="$dispatch('open-receber-parcela', @js(['id' => $parcela->id, 'descricao' => $descricao, 'valor' => Formatter::money($parcela->valor)]))">
                    Receber
                </button>
            </div>
        @endunless
    </div>
</li>
