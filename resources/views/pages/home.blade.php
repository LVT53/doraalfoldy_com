@extends('layouts.page')

@section('title', 'Kezdőlap | Alföldy Dóra')

@section('page')
    @php
        $serviceGridItems = [
            [
                'type' => 'text',
                'title' => 'Smink',
                'text' => 'Életünket számtalan esemény kíséri végig, melyeken szeretnénk a legjobb formánk hozni. Az ilyen alkalmakra készítek egyénre szabott, professzionális sminket.',
                'href' => route('smink')
            ],
            [
                'type' => 'image',
                'src' => '/images/content/DSC_52491-min.jpg',
                'alt' => 'Smink munka'
            ],
            [
                'type' => 'text',
                'title' => 'Szempilla építés',
                'text' => 'A szempilla építéssel szép és látványos hatást lehet elérni, amely kiemeli a tekintetet. Ez egy olyan technika, amivel dúsítani és hosszabbítani tudunk.',
                'href' => route('szempilla'),
                'color' => 'bg-brand-beige-header'
            ],
            [
                'type' => 'image',
                'src' => '/images/content/_SZL9234-copy-min.jpg',
                'alt' => 'Szempilla építés'
            ],
            [
                'type' => 'image',
                'src' => '/images/content/IMG_0168-min.JPG',
                'alt' => 'Szempilla munka'
            ],
            [
                'type' => 'image',
                'src' => '/images/content/DSC_4531-min.jpg',
                'alt' => 'Szemöldök munka'
            ],
            [
                'type' => 'text',
                'title' => 'Szemöldök',
                'text' => 'A szemöldök az egyik legfontosabb része az arcnak. Egy jó ívű, színű és formájú szemöldök keretet ad neki.',
                'href' => route('szemoldok'),
                'color' => 'bg-brand-beige-header'
            ],
            [
                'type' => 'text',
                'title' => 'Smink tanácsadás',
                'text' => 'Két óra, ahol teljesen a te igényeid alapján megmutatom hogyan készíts el egy hozzád illő nappali/alkalmi sminket.',
                'href' => route('smink-tanacsadas')
            ],
        ];

        $aboutButtons = [
            [
                'title' => 'Galéria',
                'text' => 'Tekintsd meg képeimet, hogy közelebb kerülhess a szolgáltatásaimhoz.',
                'href' => route('galeria')
            ],
            [
                'title' => 'Foglalj időpontot!',
                'text' => 'Bejelentkezni könnyen, pár kattintással be tudsz jelentkezni.',
                'href' => 'https://doraalfoldy.salonic.hu/'
            ]
        ];
    @endphp

    <x-sections.hero
        title="Makeup <span class='text-brand-gold'>artist.</span>"
        text="Alföldy Dóra vagyok, sminkes-, szempilla és szemöldök stylist. Turizmus szakirányon végeztem a Budapesti Gazdasági Egyetemen, ahol az utolsó évben jött egy lehetőség, hogy egy sminkes-szempilla stylist mellett tanulhatok és dolgozhatok. Ez után pedig elvégeztem a szépségtanácsadó okj-t, illetve több továbbképzésen is részt vettem. Szeretek a trendekkel haladni és képezni magam."
        secondaryTitle="Szolgáltatásaim"
        secondaryText="Sokrétű szolgáltatásaim között megtalálható az alkalmi smink, menyasszonyi smink, valamint a smink tanácsadás. Emellet szempilla építéssel (1 - 3-4D-ig) és szempilla liftinggel is foglalkozom, de a vadi új ProMade technológiájú pillák is megtalálhatók nálam. Ezeken felül pedig szemöldök szedésre, hennás festésre és laminálásra is van lehetőség."
    />

    <x-sections.service-grid :items="$serviceGridItems" />

    <div class="site-container my-10"><div class="divider !my-0"></div></div>

    <x-sections.about-split
        text="Számomra fontos a kényelmed, hogy jól érezed magad nálam, és hogy a hozzád legjobban illő sminket, szempillát és/vagy szemöldököt készítsük el. Az évek során lehetőségem nyílt többetek arcára mosolyt csalni a munkámmal. Törekszem a természetességre, de a kihívással teli látványosabb, extrább sminkeket is ugyan olyan lelkesedéssel készítem el."
        image="/images/content/Alfoldy_Dori-300dpi-1200px-RGB-790x1192-min.jpg"
        imageAlt="Alföldy Dóra"
        :buttons="$aboutButtons"
    />

    <div class="site-container my-10"><div class="divider !my-0"></div></div>

    <section class="w-[93%] md:w-[97%] mx-auto shadow-xl rounded-[15px] overflow-hidden">
        <x-ui.responsive-image src="/images/content/DSC_4825-min1.jpg" alt="Dekor kép" class="w-full h-auto object-cover" />
    </section>

    <div class="site-container my-10"><div class="divider !my-0"></div></div>

    <section class="w-[93%] md:w-[75%] mx-auto grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
        <div class="flex items-center gap-4 bg-brand-beige-light p-6 rounded-xl shadow-md border border-brand-gold/5">
            <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-sm text-brand-gold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 011.94.86l-.85 4.03a1 1 0 01-1.08.79L7.19 7.19a11.03 11.03 0 005.62 5.62l1.61-1.61a1 1 0 011.14-.23l4.03.85a1 1 0 01.86 1.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
            </div>
            <a href="tel:+36309710393" class="font-bold text-brand-gold-muted hover:text-brand-gold transition-colors text-lg">+36 30 971 0393</a>
        </div>
        <div class="flex items-center gap-4 bg-brand-beige-light p-6 rounded-xl shadow-md border border-brand-gold/5">
            <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-sm text-brand-gold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
            </div>
            <a href="mailto:info@doraalfoldy.com" class="font-bold text-brand-gold-muted hover:text-brand-gold transition-colors text-lg break-all">info@doraalfoldy.com</a>
        </div>
        <div class="flex items-center gap-4 bg-brand-beige-light p-6 rounded-xl shadow-md border border-brand-gold/5">
            <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-sm text-brand-gold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            </div>
            <a href="https://goo.gl/maps/SSDm4gxEAREqhcVz5" target="_blank" class="font-bold text-brand-gold-muted hover:text-brand-gold transition-colors text-lg">1037 Budapest, Hunor utca 56.</a>
        </div>
    </section>

    <section class="w-[93%] md:w-[75%] mx-auto grid grid-cols-1 md:grid-cols-3 gap-8 mb-20 items-stretch">
        <div class="bg-brand-beige rounded-[15px] overflow-hidden shadow-lg flex flex-col h-full border border-brand-gold/5">
            <x-ui.responsive-image src="/images/content/DSC_14481-scaled.jpg" alt="" class="w-full h-auto object-cover" />
            <div class="p-10 flex-1">
                <h3 class="text-2xl font-bold text-brand-gold mb-6 uppercase">Rugalmas időpont</h3>
                <p class="text-brand-gold-muted text-lg leading-relaxed">
                    Jobb felül a foglalásra kattintva könnyen, pár kattintással be tudsz jelentkezni, de ha van bármilyen kérdésed keress bizalommal telefonon is.
                </p>
            </div>
        </div>
        <div class="bg-brand-beige rounded-[15px] overflow-hidden shadow-lg flex flex-col h-full border border-brand-gold/5">
            <x-ui.responsive-image src="/images/content/IMG_7830-790x1185.jpg" alt="" class="w-full h-auto object-cover" />
            <div class="p-10 flex-1">
                <h3 class="text-2xl font-bold text-brand-gold mb-6 uppercase">Fizetési módok</h3>
                <p class="text-brand-gold-muted text-lg leading-relaxed">
                    Időpontod foglaló fizetésével tudod rögzíteni. A fizetéshez 15 perces időintervallum tartozik, mivel a kiválasztott időpontot zárolom a részedre.
                </p>
            </div>
        </div>
        <div class="bg-brand-beige rounded-[15px] overflow-hidden shadow-lg flex flex-col h-full border border-brand-gold/5">
            <x-ui.responsive-image src="/images/content/DSC_3073-790x1183.jpg" alt="" class="w-full h-auto object-cover" />
            <div class="p-10 flex-1">
                <h3 class="text-2xl font-bold text-brand-gold mb-4 uppercase">Kapcsolj ki!</h3>
                <p class="text-brand-gold-muted text-lg leading-relaxed">
                    A szalonban egyszerre csak egy vendég tartózkodik. Nem zavarnak meg a külső zajok, így csak a pihenésre és a szépülésre tudsz koncentrálni.
                </p>
            </div>
        </div>
    </section>

    <div class="site-container my-10"><div class="divider !my-0"></div></div>

    <section class="w-[93%] md:w-[97%] mx-auto grid grid-cols-1 md:grid-cols-3 gap-6 mb-24">
        <div class="rounded-[15px] overflow-hidden shadow-lg border border-brand-gold/5">
            <x-ui.responsive-image src="/images/content/DSC_52281-790x1183.jpg" alt="" class="w-full h-auto object-cover" />
        </div>
        <div class="rounded-[15px] overflow-hidden shadow-lg border border-brand-gold/5">
            <x-ui.responsive-image src="/images/content/DSC_5850-790x1184.jpg" alt="" class="w-full h-auto object-cover" />
        </div>
        <div class="rounded-[15px] overflow-hidden shadow-lg border border-brand-gold/5">
            <x-ui.responsive-image src="/images/content/DSC_4552.jpg" alt="" class="w-full h-auto object-cover" />
        </div>
    </section>
@endsection
