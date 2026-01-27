<footer class="footer bg-brand-beige border-t border-brand-gold/10 py-6 md:py-8 mt-16">
    <div class="w-[60%] mx-auto">
        <div class="flex flex-col lg:flex-row justify-between items-start gap-12 lg:gap-8">
            <div class="lg:w-1/3">
                <img
                    src="{{ asset('images/content/DORAALFOLDI-logo-OK-MERGED-allologo-min-min.png') }}"
                    alt="Dóra Alföldy"
                    class="w-[55%] h-auto mb-8"
                    style="filter: invert(89%) sepia(19%) saturate(1109%) hue-rotate(333deg) brightness(75%) contrast(104%);"
                >
            </div>

            <div class="flex flex-col md:flex-row gap-12 md:gap-16 lg:flex-1 justify-end">
                <div class="space-y-6">
                    <h4 class="text-xs uppercase text-brand-gold font-black">Kapcsolat</h4>
                    <ul class="space-y-4 text-base font-medium text-neutral-600">
                        <li><a href="tel:+36309710393" class="hover:text-brand-gold transition-colors flex items-center gap-3">
                            <svg class="w-5 h-5 text-brand-gold opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 011.94.86l-.85 4.03a1 1 0 01-1.08.79L7.19 7.19a11.03 11.03 0 005.62 5.62l1.61-1.61a1 1 0 011.14-.23l4.03.85a1 1 0 01.86 1.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                            +36 30 971 0393
                        </a></li>
                        <li><a href="mailto:info@doraalfoldy.com" class="hover:text-brand-gold transition-colors flex items-center gap-3">
                            <svg class="w-5 h-5 text-brand-gold opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            info@doraalfoldy.com
                        </a></li>
                        <li class="pt-2 space-y-3">
                            <a href="http://www.instagram.com/doraalfoldy_makeupartist" target="_blank" class="hover:text-brand-gold transition-colors block text-sm opacity-80 underline underline-offset-4 decoration-brand-gold/30">Instagram - Makeup Artist</a>
                            <a href="https://instagram.com/doraalfody_lashartist" target="_blank" class="hover:text-brand-gold transition-colors block text-sm opacity-80 underline underline-offset-4 decoration-brand-gold/30">Instagram - Lash Artist</a>
                            <a href="http://www.facebook.com/doraalfoldy.makeupartist" target="_blank" class="hover:text-brand-gold transition-colors block text-sm opacity-80 underline underline-offset-4 decoration-brand-gold/30">Facebook</a>
                        </li>
                    </ul>
                </div>

                <div class="space-y-6">
                    <h4 class="text-xs uppercase text-brand-gold font-black">Információk</h4>
                    <ul class="space-y-4 text-base font-medium text-neutral-600">
                        <li><a href="{{ route('gdpr') }}" class="hover:text-brand-gold transition-colors">GDPR</a></li>
                        <li><a href="{{ route('aszf') }}" class="hover:text-brand-gold transition-colors">ÁSZF</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="mt-16 pt-8 border-t border-brand-gold/10 flex flex-col md:flex-row justify-between items-center gap-8">
            <p class="text-neutral-400 text-[13px] font-medium">© {{ date('Y') }} doraalfoldy.com | Minden jog fenntartva. | Made by AlfyDesign</p>
            <div class="flex space-x-4 opacity-40 grayscale hover:grayscale-0 transition-all duration-700">
                <img src="/images/content/62e4f92fab61355be2059c66_Visa_Inc.-Logo.wine.svg" alt="Visa" class="h-6">
                <img src="/images/content/62e4f92f5d5e1d7c26c25c06_Mastercard-Logo.wine.svg" alt="Mastercard" class="h-6">
                <img src="/images/content/62e4fa45be7ad3b831c8b9b0_maestro-seeklogo.com.svg" alt="Maestro" class="h-6">
            </div>
        </div>
    </div>
</footer>
