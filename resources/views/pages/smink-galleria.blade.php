@extends('layouts.page')

@section('title', 'Smink Galéria | Alföldy Dóra')

@section('page')
    @php
        $images = [
            ['src' => '/images/content/DSC_4566-min.jpg'],
            ['src' => '/images/content/DSC_45222-min.jpg'],
            ['src' => '/images/content/DSC_53152-min.jpg'],
            ['src' => '/images/content/DSC_4565-min.jpg'],
            ['src' => '/images/content/_SZL9234-copy2-min.jpg'],
            ['src' => '/images/content/DSC_4135-min.jpg'],
            ['src' => '/images/content/DSC_4461-min.jpg'],
            ['src' => '/images/content/DSC_15341-min.jpg'],
            ['src' => '/images/content/DSC_15331-min.jpg'],
            ['src' => '/images/content/DSC_1828-min.jpg'],
            ['src' => '/images/content/DSC_486912-min.jpg'],
            ['src' => '/images/content/DSC_1378-min.jpg'],
            ['src' => '/images/content/_SZL9705-copy-32-min.jpg'],
            ['src' => '/images/content/DSC_1306-min.jpg'],
            ['src' => '/images/content/DSC_5788-min.jpg'],
            ['src' => '/images/content/_SZL9194-copy-3-min.jpg'],
            ['src' => '/images/content/DSC_1513-min.jpg'],
            ['src' => '/images/content/IMG_131-2-min.JPG'],
            ['src' => '/images/content/DSC_1516-min.jpg'],
            ['src' => '/images/content/DSC_19481-min.jpg'],
            ['src' => '/images/content/DSC_522812-min.jpg'],
            ['src' => '/images/content/DSC_144812-min.jpg'],
            ['src' => '/images/content/DSC_5380-min.jpg'],
            ['src' => '/images/content/DSC_13111-min.jpg'],
            ['src' => '/images/content/DSC_19142-min.jpg'],
            ['src' => '/images/content/_SZL9268-copy-4-min.jpg'],
            ['src' => '/images/content/IMG_01452-min.JPG'],
            ['src' => '/images/content/DSC_4314-2-min.jpg'],
            ['src' => '/images/content/DSC_52651-min.jpg'],
            ['src' => '/images/content/DSC_524412-min.jpg'],
            ['src' => '/images/content/DSC_5167-min.JPG'],
            ['src' => '/images/content/_SZL9603-copy-min.jpg'],
            ['src' => '/images/content/DSC_5536-min.jpg'],
            ['src' => '/images/content/DSC_4643-min.jpg'],
            ['src' => '/images/content/DSC_50922-min.jpg'],
            ['src' => '/images/content/DSC_4082-min.jpg'],
            ['src' => '/images/content/DSC_41452-min.jpg'],
            ['src' => '/images/content/DSC_46352-min.jpg'],
            ['src' => '/images/content/DSC_4731-min.jpg'],
            ['src' => '/images/content/DSC_4760-min.jpg'],
        ];
    @endphp

    <section class="site-container pt-12 pb-6 md:pt-16 md:pb-8 text-center">
        <div class="inline-flex items-center gap-3 mb-2">
            <div class="w-8 h-px bg-brand-gold/50"></div>
            <span class="text-brand-gold font-bold uppercase text-[10px] tracking-[0.3em]">Galéria</span>
            <div class="w-8 h-px bg-brand-gold/50"></div>
        </div>
        <h1 class="text-3xl md:text-4xl text-neutral-900 font-serif font-bold uppercase tracking-tight">Smink</h1>
    </section>

    <x-sections.masonry-grid :images="$images" />

    <section class="site-container py-12 md:py-24 text-center">
        <a href="{{ route('galeria') }}" class="inline-block border-2 border-brand-gold text-brand-gold px-10 py-3 rounded-full font-bold hover:bg-brand-gold hover:text-white transition-all duration-300">
            Vissza a galériához
        </a>
    </section>
@endsection
