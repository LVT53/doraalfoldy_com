<div class="flex items-center gap-1 bg-white/80 backdrop-blur-sm rounded-full px-2 py-1 shadow-sm border border-gray-200/50">
    <a
        href="{{ route(Route::currentRouteName(), array_merge(request()->route()->parameters(), ['lang' => 'hu'])) }}"
        wire:navigate
        class="px-3 py-1.5 rounded-full text-sm font-medium transition-all duration-300 {{ app()->getLocale() === 'hu' ? 'bg-brand-gold text-white shadow-md' : 'text-gray-600 hover:text-brand-gold hover:bg-brand-gold/10' }}"
    >
        HU
    </a>
    <span class="text-gray-300">|</span>
    <a
        href="{{ route(Route::currentRouteName(), array_merge(request()->route()->parameters(), ['lang' => 'en'])) }}"
        wire:navigate
        class="px-3 py-1.5 rounded-full text-sm font-medium transition-all duration-300 {{ app()->getLocale() === 'en' ? 'bg-brand-gold text-white shadow-md' : 'text-gray-600 hover:text-brand-gold hover:bg-brand-gold/10' }}"
    >
        EN
    </a>
</div>
