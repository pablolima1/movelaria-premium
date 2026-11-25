@php
    $bottomNavItems = [
        [
            'label' => 'Início',
            'route' => 'home',
            'active' => request()->routeIs('home'),
            'icon' => '<path d="M3.75 10.5 12 3.75l8.25 6.75V19.5a.75.75 0 0 1-.75.75h-4.5v-6h-6v6H4.5a.75.75 0 0 1-.75-.75V10.5Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>',
        ],
        [
            'label' => 'Projetos',
            'route' => 'projetos.index',
            'active' => request()->routeIs('projetos.*'),
            'icon' => '<path d="M3.75 7.5A2.25 2.25 0 0 1 6 5.25h3.19a1.5 1.5 0 0 1 1.06.44l1.06 1.06a1.5 1.5 0 0 0 1.06.44H18A2.25 2.25 0 0 1 20.25 9.44V16.5A2.25 2.25 0 0 1 18 18.75H6A2.25 2.25 0 0 1 3.75 16.5v-9Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>',
        ],
        'lancar',
        [
            'label' => 'Clientes',
            'route' => 'clientes.index',
            'active' => request()->routeIs('clientes.*'),
            'icon' => '<circle cx="12" cy="7.5" r="3.25" stroke="currentColor" stroke-width="1.6"/><path d="M5.75 19.25c0-3.45 2.8-6 6.25-6s6.25 2.55 6.25 6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>',
        ],
    ];
@endphp

<nav
    class="fixed inset-x-0 bottom-0 z-40 border-t border-gray-200 bg-white/95 pb-[env(safe-area-inset-bottom)] backdrop-blur dark:border-gray-800 dark:bg-gray-900/95 xl:hidden"
    aria-label="Navegação principal"
>
    <ul class="mx-auto flex max-w-md items-stretch justify-around">
        @foreach ($bottomNavItems as $item)
            @if ($item === 'lancar')
                <li class="flex flex-1 justify-center">
                    <button type="button" @click="$dispatch('open-lancar')"
                        class="-mt-5 flex flex-col items-center gap-1 text-[11px] font-medium text-gray-600 dark:text-gray-300"
                        aria-label="Novo lançamento">
                        <span class="flex h-13 w-13 items-center justify-center rounded-full bg-brand-500 text-white shadow-lg ring-4 ring-white transition active:scale-95 dark:ring-gray-900">
                            <svg width="26" height="26" viewBox="0 0 24 24" fill="none"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                        </span>
                        Lançar
                    </button>
                </li>
                @continue
            @endif
            <li class="flex-1">
                <a
                    href="{{ route($item['route']) }}"
                    @class([
                        'flex flex-col items-center gap-1 py-2.5 text-[11px] font-medium transition',
                        'text-brand-500 dark:text-brand-400' => $item['active'],
                        'text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200' => ! $item['active'],
                    ])
                    @if ($item['active']) aria-current="page" @endif
                >
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">{!! $item['icon'] !!}</svg>
                    {{ $item['label'] }}
                </a>
            </li>
        @endforeach
        <li class="flex-1">
            <button
                type="button"
                @click="$store.sidebar.toggleMobileOpen()"
                class="flex w-full flex-col items-center gap-1 py-2.5 text-[11px] font-medium text-gray-500 transition hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200"
            >
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M4 6.75h16M4 12h16M4 17.25h16" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" />
                </svg>
                Mais
            </button>
        </li>
    </ul>
</nav>

@include('layouts.partials.lancar-modal')
