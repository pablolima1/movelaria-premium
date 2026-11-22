<div
    class="relative"
    x-data="{ dropdownOpen: false }"
    @click.away="dropdownOpen = false"
>
    <button
        class="flex items-center text-gray-700 dark:text-gray-400"
        @click.prevent="dropdownOpen = !dropdownOpen"
        type="button"
        aria-label="Abrir menu do usuário"
    >
        <span class="mr-3 h-11 w-11 overflow-hidden rounded-full">
            <img src="{{ asset('images/support/user.jpg') }}" alt="" />
        </span>
        <span class="mr-1 block font-medium text-theme-sm">{{ auth()->user()->name }}</span>
        <svg
            class="h-5 w-5 transition-transform duration-200"
            :class="{ 'rotate-180': dropdownOpen }"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
            aria-hidden="true"
        >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
    </button>

    <div
        x-show="dropdownOpen"
        x-transition
        class="absolute right-0 z-50 mt-[17px] flex w-[260px] flex-col rounded-2xl border border-gray-200 bg-white p-3 shadow-theme-lg dark:border-gray-800 dark:bg-gray-dark"
        style="display: none;"
    >
        <div class="border-b border-gray-200 pb-3 dark:border-gray-800">
            <span class="block font-medium text-gray-700 text-theme-sm dark:text-gray-400">{{ auth()->user()->name }}</span>
            <span class="mt-0.5 block text-theme-xs text-gray-500 dark:text-gray-400">{{ auth()->user()->email }}</span>
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button
                type="submit"
                class="mt-3 flex w-full items-center gap-3 rounded-lg px-3 py-2 text-left font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-white/5"
            >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                </svg>
                Sair
            </button>
        </form>
    </div>
</div>
