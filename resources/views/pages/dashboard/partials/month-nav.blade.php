@php
    $anterior = $mes->subMonth();
    $proximo = $mes->addMonth();
    $mesAtual = now()->startOfMonth();
    $ehMesAtual = $mes->isSameMonth($mesAtual);
    $btnClass = 'inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-full border border-gray-200 bg-white text-gray-600 transition hover:bg-gray-50 hover:text-gray-800 dark:border-gray-800 dark:bg-white/[0.03] dark:text-gray-400 dark:hover:bg-white/[0.06] dark:hover:text-gray-200';
@endphp

<div class="flex items-center justify-between gap-3">
    <a href="{{ route('home', ['mes' => $anterior->format('Y-m')]) }}" class="{{ $btnClass }}" aria-label="Mês anterior">
        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M12.5 15 7.5 10l5-5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
    </a>

    <div class="text-center">
        <p class="text-lg font-semibold text-gray-800 dark:text-white/90">
            {{ ucfirst($mes->locale('pt_BR')->translatedFormat('F \d\e Y')) }}
        </p>
        @unless ($ehMesAtual)
            <a href="{{ route('home') }}" class="text-theme-xs font-medium text-brand-500 hover:text-brand-600">
                Voltar para o mês atual
            </a>
        @else
            <p class="text-theme-xs text-gray-500 dark:text-gray-400">Mês atual</p>
        @endunless
    </div>

    <a href="{{ route('home', ['mes' => $proximo->format('Y-m')]) }}" class="{{ $btnClass }}" aria-label="Próximo mês">
        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="m7.5 15 5-5-5-5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
    </a>
</div>
