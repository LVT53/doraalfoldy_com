@extends('layouts.page')

@section('title', 'Galéria | Alföldy Dóra')

@section('page')
    <section class="site-container my-12 md:my-20">
        <div class="relative lg:min-h-[75vh] flex flex-col lg:flex-row items-stretch lg:items-center">
            {{-- Main Image - Top on mobile, Shifted right on desktop --}}
            <div class="relative lg:absolute lg:right-0 lg:top-0 w-full lg:w-[70%] h-[400px] lg:h-full rounded-[30px] overflow-hidden shadow-2xl border border-brand-gold/10 order-1 lg:order-none">
                <x-ui.responsive-image
                    src="/images/content/DSC_4635-min.jpg"
                    alt="Galéria"
                    class="w-full h-full object-cover transition-transform duration-[3000ms] hover:scale-110"
                    sizes="(max-width: 1024px) 100vw, 70vw"
                />
                <div class="absolute inset-0 bg-gradient-to-r from-brand-beige-header via-transparent to-transparent hidden lg:block"></div>
                <div class="absolute inset-0 bg-brand-beige-header/20 lg:hidden backdrop-blur-[0.5px]"></div>
            </div>

            {{-- Text Content - Bottom on mobile, Floating left on desktop --}}
            <div class="relative z-10 w-full lg:w-[45%] bg-brand-beige-light lg:bg-brand-beige-light p-8 md:p-16 lg:p-20 rounded-[25px] lg:rounded-r-[25px] shadow-2xl lg:shadow-[-20px_0_50px_rgba(0,0,0,0.1)] border border-brand-gold/5 -mt-12 lg:mt-0 order-2 lg:order-none">
                <div class="inline-flex items-center gap-4 mb-8">
                    <div class="w-16 h-px bg-brand-gold"></div>
                    <span class="text-brand-gold font-bold uppercase text-[10px] md:text-xs tracking-[0.3em]">Galéria Katalógus</span>
                </div>

                <h1 class="text-4xl md:text-5xl lg:text-6xl font-serif font-bold text-neutral-900 leading-[0.85] tracking-tighter mb-10">
                    Munkáim <span class="text-brand-gold">&</span> Stílus
                </h1>

                <p class="header-para text-lg md:text-xl text-neutral-600 mb-12 max-w-sm leading-relaxed font-medium italic">
                    Fedezd fel a legújabb smink, szempilla és szemöldök munkáimat egy helyen.
                </p>

                {{-- Selected Design: Staggered Portfolio Stack --}}
                <div class="group/stack">
                    <div class="flex items-center gap-8">
                        <div class="flex items-end -space-x-2">
                            <div class="w-12 h-16 rounded-lg overflow-hidden shadow-md border border-brand-gold/10 -rotate-6 group-hover/stack:rotate-0 transition-transform duration-500">
                                <img src="/images/content/DSC_4145-min.jpg" class="w-full h-full object-cover" alt="">
                            </div>
                            <div class="w-12 h-16 rounded-lg overflow-hidden shadow-lg border border-brand-gold/20 z-10 translate-y-2 group-hover/stack:translate-y-0 transition-transform duration-500">
                                <img src="/images/content/IMG_9163-510x616.jpg" class="w-full h-full object-cover" alt="">
                            </div>
                            <div class="w-12 h-16 rounded-lg overflow-hidden shadow-md border border-brand-gold/10 rotate-6 group-hover/stack:rotate-0 transition-transform duration-500">
                                <img src="/images/content/image1-510x765.jpeg" class="w-full h-full object-cover" alt="">
                            </div>
                        </div>
                        <span class="text-neutral-400 text-xs font-bold tracking-[0.2em] uppercase">Stúdió Pillanatok</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="w-[93%] md:w-[75%] mx-auto my-24"><div class="divider"></div></div>

    {{-- Gallery Grid --}}
    <section class="site-container grid grid-cols-1 md:grid-cols-3 gap-10 mb-32">
        <a href="{{ route('szempilla-galeria') }}" class="group relative h-[500px] md:h-[700px] rounded-[30px] overflow-hidden shadow-xl transition-all duration-700 hover:-translate-y-3">
            <x-ui.responsive-image src="/images/content/IMG_9163-510x616.jpg" alt="Szempilla" class="absolute inset-0 w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110" />
            <div class="absolute inset-0 bg-gradient-to-t from-neutral-950/95 via-neutral-900/20 to-transparent opacity-80 group-hover:opacity-90 transition-opacity"></div>
            <div class="absolute bottom-12 left-10 right-10">
                <span class="text-brand-gold text-xs font-black tracking-widest uppercase mb-4 block opacity-0 group-hover:opacity-100 translate-y-4 group-hover:translate-y-0 transition-all duration-500">Megtekintés</span>
                <p class="text-2xl md:text-3xl lg:text-4xl font-serif font-bold text-white mb-6 leading-none">Szempilla</p>
                <div class="w-12 h-1.5 bg-brand-gold transition-all duration-700 group-hover:w-full rounded-full"></div>
            </div>
        </a>

        <a href="{{ route('smink-galleria') }}" class="group relative h-[500px] md:h-[700px] rounded-[30px] overflow-hidden shadow-xl transition-all duration-700 hover:-translate-y-3">
            <x-ui.responsive-image src="/images/content/DSC_47851-510x764.jpg" alt="Smink" class="absolute inset-0 w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110" />
            <div class="absolute inset-0 bg-gradient-to-t from-neutral-950/95 via-neutral-900/20 to-transparent opacity-80 group-hover:opacity-90 transition-opacity"></div>
            <div class="absolute bottom-12 left-10 right-10">
                <span class="text-brand-gold text-xs font-black tracking-widest uppercase mb-4 block opacity-0 group-hover:opacity-100 translate-y-4 group-hover:translate-y-0 transition-all duration-500">Megtekintés</span>
                <p class="text-2xl md:text-3xl lg:text-4xl font-serif font-bold text-white mb-6 leading-none">Smink</p>
                <div class="w-12 h-1.5 bg-brand-gold transition-all duration-700 group-hover:w-full rounded-full"></div>
            </div>
        </a>

        <a href="{{ route('szemoldok-galleria') }}" class="group relative h-[500px] md:h-[700px] rounded-[30px] overflow-hidden shadow-xl transition-all duration-700 hover:-translate-y-3">
            <x-ui.responsive-image src="/images/content/image1-510x765.jpeg" alt="Szemöldök" class="absolute inset-0 w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110" />
            <div class="absolute inset-0 bg-gradient-to-t from-neutral-950/95 via-neutral-900/20 to-transparent opacity-80 group-hover:opacity-90 transition-opacity"></div>
            <div class="absolute bottom-12 left-10 right-10">
                <span class="text-brand-gold text-xs font-black tracking-widest uppercase mb-4 block opacity-0 group-hover:opacity-100 translate-y-4 group-hover:translate-y-0 transition-all duration-500">Megtekintés</span>
                <p class="text-2xl md:text-3xl lg:text-4xl font-serif font-bold text-white mb-6 leading-none">Szemöldök</p>
                <div class="w-12 h-1.5 bg-brand-gold transition-all duration-700 group-hover:w-full rounded-full"></div>
            </div>
        </a>
    </section>
@endsection
