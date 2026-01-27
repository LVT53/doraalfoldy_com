@props([
    'notice' => config('site.notice'),
])

<nav class="navbar sticky top-0 z-[10000]">
    @if($notice)
        <div class="bg-blue-600 text-white py-3 px-4">
            <p class="w-[93%] md:w-[87%] mx-auto text-center text-xs md:text-sm font-medium tracking-wide leading-relaxed">{{ $notice }}</p>
        </div>
    @endif

    <div class="backdrop-blur-[20px] bg-white/80 border-b border-brand-gold/5">
        <div class="w-[93%] md:w-[87%] mx-auto py-3 flex justify-between items-center text-neutral-600">
        <a href="{{ route('home') }}" class="flex-shrink-0">
            <img
                src="{{ asset('images/content/DORAALFOLDI-logo-OK-MERGED-alaplogo.png') }}"
                alt="Dóra Alföldy"
                class="w-[170px] md:w-[200px] lg:w-[240px] max-w-full h-auto"
                style="filter: invert(89%) sepia(19%) saturate(1109%) hue-rotate(333deg) brightness(75%) contrast(104%);"
            >
        </a>

        <div class="hidden lg:flex items-center space-x-2">
            <a href="{{ route('home') }}" class="nav-link px-3 py-1 text-[14px] font-bold transition-colors {{ request()->routeIs('home') ? 'text-brand-gold underline underline-offset-4 decoration-2' : 'hover:text-brand-gold' }}">Kezdőlap</a>

            <div x-data="{ open: false }" class="relative py-1" @mouseenter="open = true" @mouseleave="open = false">
                <button class="nav-link px-3 text-[14px] font-bold flex items-center transition-colors {{ request()->routeIs('smink', 'szempilla', 'szemoldok', 'smink-tanacsadas') ? 'text-brand-gold underline underline-offset-4 decoration-2' : 'hover:text-brand-gold' }}">
                    Szolgáltatások
                    <svg class="ml-1.5 h-3.5 w-3.5 transition-transform duration-300" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div x-show="open"
                     class="absolute left-0 top-full pt-2 w-56 z-50">
                    <div class="bg-white/95 backdrop-blur-md shadow-lg rounded-xl py-3 border border-brand-gold/10 overflow-hidden"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0">
                        <a href="{{ route('szempilla') }}" class="block px-5 py-2 text-[14px] font-semibold hover:text-brand-gold hover:bg-brand-beige/30 transition-all">Szempilla</a>
                        <a href="{{ route('smink') }}" class="block px-5 py-2 text-[14px] font-semibold hover:text-brand-gold hover:bg-brand-beige/30 transition-all">Smink</a>
                        <a href="{{ route('szemoldok') }}" class="block px-5 py-2 text-[14px] font-semibold hover:text-brand-gold hover:bg-brand-beige/30 transition-all">Szemöldök</a>
                        <a href="{{ route('smink-tanacsadas') }}" class="block px-5 py-2 text-[14px] font-semibold hover:text-brand-gold hover:bg-brand-beige/30 transition-all">Smink tanácsadás</a>
                    </div>
                </div>
            </div>

            <a href="{{ route('galeria') }}" class="nav-link px-3 py-1 text-[14px] font-bold transition-colors {{ request()->routeIs('galeria*') ? 'text-brand-gold underline underline-offset-4 decoration-2' : 'hover:text-brand-gold' }}">Galéria</a>

            <div x-data="{ open: false }" class="relative py-1" @mouseenter="open = true" @mouseleave="open = false">
                <button class="nav-link px-3 text-[14px] font-bold flex items-center transition-colors {{ request()->routeIs('gdpr', 'aszf') ? 'text-brand-gold underline underline-offset-4 decoration-2' : 'hover:text-brand-gold' }}">
                    Info
                    <svg class="ml-1.5 h-3.5 w-3.5 transition-transform duration-300" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div x-show="open"
                     class="absolute left-0 top-full pt-2 w-48 z-50">
                    <div class="bg-white/95 backdrop-blur-md shadow-lg rounded-xl py-3 border border-brand-gold/10 overflow-hidden"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0">
                        <a href="{{ route('gdpr') }}" class="block px-5 py-2 text-[14px] font-semibold hover:text-brand-gold hover:bg-brand-beige/30 transition-all">GDPR</a>
                        <a href="{{ route('aszf') }}" class="block px-5 py-2 text-[14px] font-semibold hover:text-brand-gold hover:bg-brand-beige/30 transition-all">ÁSZF</a>
                    </div>
                </div>
            </div>

            <div class="w-px h-6 bg-brand-gold/20 mx-4"></div>

            <a href="https://doraalfoldy.salonic.hu/" class="bg-brand-gold text-white px-6 py-2 rounded-full text-[13px] font-bold hover:bg-brand-gold-muted transition-all duration-500 shadow-gold hover:scale-105">Foglalj időpontot!</a>
        </div>

        <!-- Mobile menu toggle -->
        <div class="lg:hidden" x-data="{ mobileMenuOpen: false }">
             <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-neutral-900 p-2 focus:outline-none">
                <svg x-show="!mobileMenuOpen" class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                <svg x-show="mobileMenuOpen" class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
             </button>

             <div x-show="mobileMenuOpen"
                  x-transition:enter="transition ease-out duration-300"
                  x-transition:enter-start="opacity-0 -translate-y-4"
                  x-transition:enter-end="opacity-100 translate-y-0"
                  class="fixed inset-x-0 top-full h-[calc(100vh-100%)] bg-white/95 backdrop-blur-2xl shadow-2xl py-12 px-8 flex flex-col space-y-8 z-[9999] overflow-y-auto">
                 <a href="{{ route('home') }}" class="text-xl font-serif font-bold {{ request()->routeIs('home') ? 'text-brand-gold' : 'text-neutral-900' }}" @click="mobileMenuOpen = false">Kezdőlap</a>

                <div x-data="{ open: true }">
                    <button @click="open = !open" class="text-xl font-serif font-bold text-neutral-900 flex justify-between w-full items-center">
                        Szolgáltatások
                        <svg class="h-6 w-6 transition-transform duration-300" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="open" class="pl-6 mt-4 flex flex-col space-y-4 border-l-2 border-brand-gold/20">
                        <a href="{{ route('szempilla') }}" class="text-lg font-medium text-neutral-600 hover:text-brand-gold" @click="mobileMenuOpen = false">Szempilla</a>
                        <a href="{{ route('smink') }}" class="text-lg font-medium text-neutral-600 hover:text-brand-gold" @click="mobileMenuOpen = false">Smink</a>
                        <a href="{{ route('szemoldok') }}" class="text-lg font-medium text-neutral-600 hover:text-brand-gold" @click="mobileMenuOpen = false">Szemöldök</a>
                        <a href="{{ route('smink-tanacsadas') }}" class="text-lg font-medium text-neutral-600 hover:text-brand-gold" @click="mobileMenuOpen = false">Smink tanácsadás</a>
                    </div>
                </div>

                <a href="{{ route('galeria') }}" class="text-xl font-serif font-bold {{ request()->routeIs('galeria*') ? 'text-brand-gold' : 'text-neutral-900' }}" @click="mobileMenuOpen = false">Galéria</a>

                <div x-data="{ open: false }">
                    <button @click="open = !open" class="text-xl font-serif font-bold text-neutral-900 flex justify-between w-full items-center">
                        Info
                        <svg class="h-6 w-6 transition-transform duration-300" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="open" class="pl-6 mt-4 flex flex-col space-y-4 border-l-2 border-brand-gold/20">
                        <a href="{{ route('gdpr') }}" class="text-lg font-medium text-neutral-600" @click="mobileMenuOpen = false">GDPR</a>
                        <a href="{{ route('aszf') }}" class="text-lg font-medium text-neutral-600" @click="mobileMenuOpen = false">ÁSZF</a>
                    </div>
                </div>

                <div class="pt-4 text-center">
                    <a href="https://doraalfoldy.salonic.hu/" class="inline-block bg-brand-gold text-white px-8 py-4 rounded-full font-bold text-lg shadow-xl active:scale-95 transition-all">Foglalj időpontot!</a>
                </div>
             </div>
        </div>
    </div>
    </div>
</nav>
