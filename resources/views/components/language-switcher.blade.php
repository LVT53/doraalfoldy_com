<div class="flex items-center gap-2 text-sm">
    <a
        href="{{ request()->fullUrlWithQuery(['lang' => 'hu']) }}"
        class="px-2 py-1 rounded transition-colors {{ app()->getLocale() === 'hu' ? 'font-bold text-brand-gold' : 'text-gray-600 hover:text-brand-gold' }}"
    >
        HU
    </a>
    <span class="text-gray-400">|</span>
    <a
        href="{{ request()->fullUrlWithQuery(['lang' => 'en']) }}"
        class="px-2 py-1 rounded transition-colors {{ app()->getLocale() === 'en' ? 'font-bold text-brand-gold' : 'text-gray-600 hover:text-brand-gold' }}"
    >
        EN
    </a>
</div>
