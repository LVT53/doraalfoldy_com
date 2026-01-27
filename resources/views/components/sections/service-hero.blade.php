@props([
    'title' => null,
    'text' => null,
    'secondaryText' => null,
    'image' => null,
    'imageAlt' => '',
    'category' => 'Szolgáltatás',
    'width' => 'w-[87%]',
    'sizes' => '(max-width: 1024px) 100vw, 70vw',
])

<section class="w-[93%] md:{{ $width }} mx-auto my-12 md:my-20">
    <div class="relative min-h-[50vh] md:min-h-[65vh] flex items-center mb-12 lg:mb-20">
        {{-- Main Image - Shifted right --}}
        <div class="absolute right-0 top-0 w-full lg:w-[70%] h-full rounded-[30px] overflow-hidden shadow-2xl border border-brand-gold/10">
            @if($image)
                <x-ui.responsive-image
                    src="{{ $image }}"
                    alt="{{ $imageAlt }}"
                    class="w-full h-full object-cover transition-transform duration-[3000ms] hover:scale-110"
                    :sizes="$sizes"
                />
            @endif
            <div class="absolute inset-0 bg-gradient-to-r from-brand-beige-header via-transparent to-transparent hidden lg:block"></div>
            <div class="absolute inset-0 bg-brand-beige-header/40 lg:hidden backdrop-blur-[1px]"></div>
        </div>

        {{-- Text Content - Floating left --}}
        <div class="relative z-10 w-full lg:w-[45%] bg-brand-beige-light/95 lg:bg-brand-beige-light p-8 md:p-12 lg:p-16 rounded-[25px] lg:rounded-r-[25px] shadow-2xl lg:shadow-[-20px_0_50px_rgba(0,0,0,0.1)] border border-brand-gold/5">
            <div class="inline-flex items-center gap-4 mb-6">
                <div class="w-12 h-px bg-brand-gold"></div>
                <span class="text-brand-gold font-bold uppercase text-[10px] md:text-xs tracking-[0.3em]">{{ $category }}</span>
            </div>

            <h1 class="text-4xl md:text-5xl lg:text-6xl font-serif font-bold text-neutral-900 leading-[0.85] tracking-tighter mb-8">
                {!! $title !!}
            </h1>

            <p class="header-para text-base md:text-lg text-neutral-600 mb-0 leading-relaxed font-medium italic">
                {{ $text }}
            </p>
        </div>
    </div>

    @if($secondaryText)
        <div class="max-w-4xl mx-auto text-center px-4">
            <div class="inline-flex flex-col items-center">
                <div class="w-px h-12 bg-brand-gold/30 mb-8"></div>
                <div class="header-para text-lg md:text-xl leading-relaxed text-neutral-700 italic font-medium">
                    {!! $secondaryText !!}
                </div>
            </div>
        </div>
    @endif
</section>
