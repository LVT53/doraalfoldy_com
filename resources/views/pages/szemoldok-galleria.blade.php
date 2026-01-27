@extends('layouts.page')

@section('title', 'Szemöldök Galéria | Alföldy Dóra')

@section('page')
    @php
        $images = [
            ['src' => '/images/content/C8F3C557-1E28-4E57-A098-4E8E984EEDCC-min.JPEG'],
            ['src' => '/images/content/4C57FC68-7D51-44F6-9CD6-51F1C9D78240-min.jpg'],
            ['src' => '/images/content/49FF2092-EB0D-4C7C-A6D7-E8E40E5CFF83-min.JPEG'],
            ['src' => '/images/content/D7F0562E-68C5-48B2-9F15-E9379C4F6644-min.JPEG'],
        ];
    @endphp

    <section class="site-container pt-12 pb-6 md:pt-16 md:pb-8 text-center">
        <div class="inline-flex items-center gap-3 mb-2">
            <div class="w-8 h-px bg-brand-gold/50"></div>
            <span class="text-brand-gold font-bold uppercase text-[10px] tracking-[0.3em]">Galéria</span>
            <div class="w-8 h-px bg-brand-gold/50"></div>
        </div>
        <h1 class="text-3xl md:text-4xl text-neutral-900 font-serif font-bold uppercase tracking-tight">Szemöldök</h1>
    </section>

    <x-sections.masonry-grid :images="$images" />

    <section class="site-container py-12 md:py-24 text-center">
        <a href="{{ route('galeria') }}" class="inline-block border-2 border-brand-gold text-brand-gold px-10 py-3 rounded-full font-bold hover:bg-brand-gold hover:text-white transition-all duration-300">
            Vissza a galériához
        </a>
    </section>
@endsection
