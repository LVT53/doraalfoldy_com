@props([
    'title' => 'Szalon és fizetési lehetőségek',
    'address' => '1037 Budapest, Hunor utca 56.',
    'addressLink' => 'https://goo.gl/maps/EZMLbpvSF6dCJbVA6',
    'email' => 'info@doraalfoldy.com',
    'phone' => '+36 30 971 0393',
])

<section class="w-[93%] md:w-[75%] mx-auto mb-24 relative group">
    {{-- Decorative background elements --}}
    <div class="absolute -inset-4 bg-brand-gold/5 rounded-[40px] blur-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-1000"></div>

    <div class="relative bg-white rounded-[30px] border border-brand-gold/10 shadow-xl overflow-hidden">
        <div class="grid grid-cols-1 lg:grid-cols-12">
            {{-- Left Side: Content --}}
            <div class="lg:col-span-7 p-8 md:p-12 lg:p-16 bg-brand-beige/30">
                <div class="flex items-center gap-4 mb-8">
                    <div class="w-12 h-px bg-brand-gold"></div>
                    <h2 class="text-3xl md:text-4xl font-serif font-bold text-brand-gold leading-tight uppercase tracking-tight">
                        {{ $title }}
                    </h2>
                </div>

                <div class="space-y-8">
                    <div class="flex gap-6">
                        <div class="shrink-0 w-12 h-12 rounded-full bg-brand-gold/10 flex items-center justify-center text-brand-gold">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        </div>
                        <div>
                            <p class="font-medium">
                                Barátságos, nyugodt környezetben várlak a 3. kerületben a Remetehegy lábánál, amely könnyen megközelíthető autóval és tömegközlekedéssel egyaránt.
                            </p>
                        </div>
                    </div>

                    <div class="flex gap-6">
                        <div class="shrink-0 w-12 h-12 rounded-full bg-brand-gold/10 flex items-center justify-center text-brand-gold">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <p class="text-neutral-600 italic">
                                Időpontod foglaló fizetésével tudod rögzíteni. A fizetéshez 15 perces időintervallum tartozik, mivel a kiválasztott időpontot zároljuk a részedre. Foglalásod abban az esetben érvényes, ha az emailben visszaigazolásra került. Amennyiben a lefoglalt időpontot módosítani szeretnéd azt kérlek jelezd felém. Ezt megteheted a honlapon, emailben és telefonon is. Időpont módosításra a lefoglalt időpont előtt 72 órával van lehetőség, amennyiben a foglalási díjat szeretnéd felhasználni a következő időpontra.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Side: Contact Info --}}
            <div class="lg:col-span-5 bg-brand-beige-header p-8 md:p-12 lg:p-16 flex flex-col justify-center text-neutral-600 relative">
                {{-- Background pattern --}}
                <div class="absolute inset-0 opacity-10 pointer-events-none">
                    <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                        <path d="M0 100 C 20 0 50 0 100 100" fill="none" stroke="white" stroke-width="0.5" />
                        <path d="M0 80 C 30 20 60 20 100 80" fill="none" stroke="white" stroke-width="0.5" />
                    </svg>
                </div>

                <div class="relative space-y-8">
                    <div class="group/item">
                        <span class="text-label !text-neutral-700 opacity-60 block mb-2">Helyszín</span>
                        <a href="{{ $addressLink }}" target="_blank" class="text-xl md:text-2xl font-serif font-bold hover:text-neutral-400 transition-colors flex items-start gap-3">
                            <svg class="w-6 h-6 mt-1 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            {{ $address }}
                        </a>
                    </div>

                    <div class="group/item">
                        <span class="text-label !text-neutral-700 opacity-60 block mb-2">Email</span>
                        <a href="mailto:{{ $email }}" class="text-xl md:text-2xl font-serif font-bold hover:text-neutral-400 transition-colors flex items-start gap-3">
                            <svg class="w-6 h-6 mt-1 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            {{ $email }}
                        </a>
                    </div>

                    <div class="group/item">
                        <span class="text-label !text-neutral-700 opacity-60 block mb-2">Telefon</span>
                        <a href="tel:{{ str_replace(' ', '', $phone) }}" class="text-xl md:text-2xl font-serif font-bold hover:text-neutral-400 transition-colors flex items-start gap-3">
                            <svg class="w-6 h-6 mt-1 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 5a2 2 0 012-2h3.28a1 1 0 011.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                            {{ $phone }}
                        </a>
                    </div>
                    </div>


                </div>
            </div>
        </div>
    </div>
</section>
