@extends('layouts.page')

@section('title', 'Szempilla Galéria | Alföldy Dóra')

@section('page')
    @php
        $images = [
            ['src' => '/images/content/IMG_1711-2-min2.jpg'],
            ['src' => '/images/content/IMG_0762-min.jpg'],
            ['src' => '/images/content/IMG_0292-min.JPG'],
            ['src' => '/images/content/IMG_9196_2-min.jpg'],
            ['src' => '/images/content/IMG_0291-min.JPG'],
            ['src' => '/images/content/IMG_4854-min.jpg'],
            ['src' => '/images/content/IMG_1780-3-min.jpg'],
            ['src' => '/images/content/IMG_0951-min.jpg'],
            ['src' => '/images/content/IMG_4667-min.jpg'],
            ['src' => '/images/content/IMG_2841-min.jpg'],
            ['src' => '/images/content/IMG_4677-min2.jpg'],
            ['src' => '/images/content/IMG_0771-min_1.jpg'],
            ['src' => '/images/content/IMG_0362-min.jpg'],
            ['src' => '/images/content/IMG_0361-min.jpg'],
            ['src' => '/images/content/DSC_7844-min2.jpg'],
            ['src' => '/images/content/IMG_0942-min.jpg'],
            ['src' => '/images/content/IMG_2840-min.JPG'],
            ['src' => '/images/content/DSC_7854-min_1.jpg'],
            ['src' => '/images/content/IMG_1715-min2.jpg'],
            ['src' => '/images/content/IMG_0784-min.JPG'],
            ['src' => '/images/content/IMG_4678-min2.jpg'],
            ['src' => '/images/content/IMG_1786-min.JPG'],
            ['src' => '/images/content/DSC_7830-min.jpg'],
            ['src' => '/images/content/DSC_7877-min.jpg'],
            ['src' => '/images/content/DSC_7870-min.jpg'],
            ['src' => '/images/content/DSC_7840-min2.jpg'],
            ['src' => '/images/content/DSC_7810-min3.jpg'],
            ['src' => '/images/content/IMG_7902.JPG'],
            ['src' => '/images/content/IMG_8480.JPG'],
            ['src' => '/images/content/IMG_8482.JPG'],
        ];
    @endphp

    <section class="site-container pt-12 pb-6 md:pt-16 md:pb-8 text-center">
        <div class="inline-flex items-center gap-3 mb-2">
            <div class="w-8 h-px bg-brand-gold/50"></div>
            <span class="text-brand-gold font-bold uppercase text-[10px] tracking-[0.3em]">Galéria</span>
            <div class="w-8 h-px bg-brand-gold/50"></div>
        </div>
        <h1 class="text-3xl md:text-4xl text-neutral-900 font-serif font-bold uppercase tracking-tight">Szempilla</h1>
    </section>

    <x-sections.masonry-grid :images="$images" />

    <section class="site-container py-12 md:py-24 text-center">
        <a href="{{ route('galeria') }}" class="inline-block border-2 border-brand-gold text-brand-gold px-10 py-3 rounded-full font-bold hover:bg-brand-gold hover:text-white transition-all duration-300">
            Vissza a galériához
        </a>
    </section>
@endsection
