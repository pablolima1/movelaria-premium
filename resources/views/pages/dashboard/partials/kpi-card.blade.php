@php
    $variants = [
        'brand' => ['icon' => 'bg-brand-50 text-brand-500 dark:bg-brand-500/15 dark:text-brand-400', 'value' => 'text-gray-800 dark:text-white/90'],
        'success' => ['icon' => 'bg-success-50 text-success-600 dark:bg-success-500/15 dark:text-success-500', 'value' => 'text-success-600 dark:text-success-500'],
        'error' => ['icon' => 'bg-error-50 text-error-600 dark:bg-error-500/15 dark:text-error-500', 'value' => 'text-error-600 dark:text-error-500'],
        'warning' => ['icon' => 'bg-warning-50 text-warning-600 dark:bg-warning-500/15 dark:text-orange-400', 'value' => 'text-gray-800 dark:text-white/90'],
        'gray' => ['icon' => 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-300', 'value' => 'text-gray-800 dark:text-white/90'],
    ];
    $v = $variants[$variant ?? 'gray'];
    $icons = [
        'entrada' => '<path d="M12 4.5v15m0 0-5.25-5.25M12 19.5l5.25-5.25" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>',
        'lucro' => '<path d="M3.75 17.25 9 12l4 4 7.25-7.25M20.25 8.75V13m0-4.25H16" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>',
        'bruto' => '<path d="M4 19.5h16M6.5 16V10m5.5 6V6m5.5 10v-4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>',
        'imposto' => '<path d="M17.5 6.5l-11 11" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/><circle cx="7.75" cy="7.75" r="2.25" stroke="currentColor" stroke-width="1.6"/><circle cx="16.25" cy="16.25" r="2.25" stroke="currentColor" stroke-width="1.6"/>',
    ];
@endphp

<div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03] sm:p-5">
    <div class="flex items-center gap-2.5">
        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg sm:h-10 sm:w-10 sm:rounded-xl {{ $v['icon'] }}">
            <svg class="h-4 w-4 sm:h-5 sm:w-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">{!! $icons[$icon] ?? '' !!}</svg>
        </span>
        <p class="text-theme-xs leading-tight text-gray-500 sm:text-sm dark:text-gray-400">{{ $label }}</p>
    </div>
    <p class="mt-3 text-lg font-bold tracking-tight sm:text-2xl {{ $v['value'] }}" title="{{ \App\Helpers\Formatter::money($value) }}">
        <span class="sm:hidden">{{ \App\Helpers\Formatter::moneyShort($value) }}</span>
        <span class="hidden sm:inline">{{ \App\Helpers\Formatter::money($value) }}</span>
    </p>
    @isset($hint)
        <p class="mt-1 truncate text-theme-xs text-gray-500 dark:text-gray-400">{{ $hint }}</p>
    @endisset
</div>
