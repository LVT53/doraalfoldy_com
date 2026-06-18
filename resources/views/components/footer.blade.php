<footer class="footer bg-brand-beige border-t border-brand-gold/10 py-6 md:py-8 mt-16">
    <div class="max-w-[1700px] w-[93%] md:w-[60%] mx-auto">
        <div class="flex flex-col lg:flex-row justify-between items-start gap-8 lg:gap-8">
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
                    <h4 class="font-serif font-bold text-lg md:text-xl text-brand-gold mb-2">Kapcsolat</h4>
                    <ul class="space-y-4 font-medium text-neutral-600">
                        <li><a href="tel:+36309710393" class="hover:text-brand-gold transition-colors flex items-center gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-brand-gold opacity-60" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.256c-.502-.143-.974.173-1.137.648l-.7 2.036a1.125 1.125 0 0 1-1.246.764 11.97 11.97 0 0 1-7.06-7.06 1.125 1.125 0 0 1 .764-1.246l2.036-.7c.475-.163.791-.635.648-1.137L8.23 3.352a1.125 1.125 0 0 0-1.091-.852H5.25A2.25 2.25 0 0 0 3 4.75v2z"/></svg>
                            +36 30 971 0393
                        </a></li>
                        <li><a href="mailto:info@doraalfoldy.com" class="hover:text-brand-gold transition-colors flex items-center gap-3">
                            <svg class="w-5 h-5 text-brand-gold opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            info@doraalfoldy.com
                        </a></li>
                        <li class="pt-2 space-y-3">
                            <a href="http://www.instagram.com/doraalfoldy_makeupartist" target="_blank" class="hover:text-brand-gold transition-colors block text-[13px] opacity-80 underline underline-offset-4 decoration-brand-gold/30">Instagram - Makeup Artist</a>
                            <a href="https://instagram.com/doraalfody_lashartist" target="_blank" class="hover:text-brand-gold transition-colors block text-[13px] opacity-80 underline underline-offset-4 decoration-brand-gold/30">Instagram - Lash Artist</a>
                            <a href="http://www.facebook.com/doraalfoldy.makeupartist" target="_blank" class="hover:text-brand-gold transition-colors block text-[13px] opacity-80 underline underline-offset-4 decoration-brand-gold/30">Facebook</a>
                        </li>
                    </ul>
                </div>

                <div class="space-y-6">
                    <h4 class="font-serif font-bold text-lg md:text-xl text-brand-gold mb-2">Információk</h4>
                    <ul class="space-y-4 font-medium text-neutral-600">
                        <li><a href="{{ route('gdpr') }}" class="hover:text-brand-gold transition-colors">GDPR</a></li>
                        <li><a href="{{ route('aszf') }}" class="hover:text-brand-gold transition-colors">ÁSZF</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="mt-16 pt-8 border-t border-brand-gold/10 flex flex-col md:flex-row justify-between items-center gap-6 md:gap-8">
            <p class="text-neutral-400 text-[13px] font-medium mb-0 self-center text-center md:text-left">© {{ date('Y') }} doraalfoldy.com | Minden jog fenntartva. | Designed & coded by AlfyDesign</p>
            <div class="flex items-center space-x-4 opacity-40 grayscale hover:grayscale-0 transition-all duration-700 self-center shrink-0">
                <img src="/images/content/62e4f92fab61355be2059c66_Visa_Inc.-Logo.wine.svg" alt="Visa" class="h-6">
                <img src="/images/content/62e4f92f5d5e1d7c26c25c06_Mastercard-Logo.wine.svg" alt="Mastercard" class="h-6">
                <img src="/images/content/62e4fa45be7ad3b831c8b9b0_maestro-seeklogo.com.svg" alt="Maestro" class="h-6">
            </div>
        </div>
    </div>
</footer>
