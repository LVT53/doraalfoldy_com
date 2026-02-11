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

<section class="w-[93%] md:{{ $width }} mx-auto lg:my-12">
    <div class="relative lg:min-h-[65vh] flex flex-col lg:flex-row items-stretch lg:items-center mb-12 lg:mb-20">
        {{-- Main Image - Top on mobile, Shifted right on desktop --}}
        <div class="relative lg:absolute lg:right-0 lg:top-0 w-full lg:w-[70%] h-[350px] md:h-[450px] lg:h-full rounded-[30px] overflow-hidden shadow-2xl border border-brand-gold/10 order-1 lg:order-none">
            @if($image)
                <x-ui.responsive-image
                    src="{{ $image }}"
                    alt="{{ $imageAlt }}"
                    class="w-full h-full object-cover transition-transform duration-[3000ms] hover:scale-110"
                    :sizes="$sizes"
                />
            @endif
            <div class="absolute inset-0 bg-gradient-to-r from-brand-beige-header via-transparent via-30% to-transparent hidden lg:block"></div>
            <div class="absolute inset-0 lg:bg-brand-beige-header/20 lg:hidden backdrop-blur-[0.5px]"></div>
        </div>

        {{-- Text Content - Bottom on mobile, Floating left on desktop --}}
        <div class="relative z-10 w-full lg:w-[45%] bg-brand-beige-light lg:bg-brand-beige-light p-8 md:p-12 lg:p-16 rounded-[25px] lg:rounded-r-[25px] shadow-2xl lg:shadow-[-20px_0_50px_rgba(0,0,0,0.1)] border border-brand-gold/5 -mt-12 lg:mt-0 order-2 lg:order-none">
            <div class="inline-flex items-center gap-4 mb-6">
                <div class="w-12 h-px bg-brand-gold"></div>
                <span class="text-label">{{ $category }}</span>
            </div>

            <h1 class="mb-8">
                {!! $title !!}
            </h1>

            <p class="header-para text-neutral-600 mb-0 italic font-medium">
                {{ $text }}
            </p>
        </div>
    </div>

    @if($secondaryText)
        <div class="max-w-4xl mx-auto text-center px-4">
            <div class="inline-flex flex-col items-center">
                <div class="w-px h-12 bg-brand-gold/30 mb-8"></div>
                <div class="header-para text-neutral-700 italic font-medium">
                    {!! $secondaryText !!}
                </div>
            </div>
        </div>
    @endif
</section>
