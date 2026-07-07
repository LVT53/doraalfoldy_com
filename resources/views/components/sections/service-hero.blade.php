@props([
    'title' => null,
    'text' => null,
    'secondaryText' => null,
    'image' => null,
    'imageAlt' => '',
    'category' => 'Szolgáltatás',
    'width' => 'w-[87%]',
    'sizes' => '(max-width: 767px) 525px, (max-width: 1279px) 675px, 70vw',
    'overlayVia' => '30%',
    'wideImage' => false,
])

@php
    $overlayViaClass = match ($overlayVia) {
        '12%' => 'via-12%',
        default => 'via-30%',
    };
    // Below `xl` the image box is `100vw` wide but a FIXED height (350px,
    // then 450px from `md`), so on phones its aspect ratio (~1.07-1.7:1)
    // ends up narrower than the source photos' fixed 3:2 (1.5:1) landscape
    // crop. That flips object-cover into height-constrained scaling: the
    // box needs a source at least `height * 1.5` px wide to cover it
    // without upscaling, which is MORE than the box's own CSS width once
    // height exceeds width / 1.5 (true for every real phone at these fixed
    // heights). `100vw`-based srcset selection only reasons about width, so
    // it was picking sources 30-40% short of what's actually needed and
    // the browser silently upscaled them - the "pixelation" on mobile.
    // Reporting the height-derived width instead (350*1.5=525,
    // 450*1.5=675) makes the browser request enough source resolution to
    // cover the box's real, height-bound scale.
    // The hero box's width is capped by the page's max-w-[1700px] container,
    // but its height used to keep growing with 65vh. On tall desktop viewports
    // (1440p, 4K) that made the box far narrower than the photo's 3:2 aspect
    // ratio, cropping deep into whoever's on the left. Capping the row's
    // height at 600px keeps the crop at roughly the same (mild) level as a
    // typical 1080p screen, however tall the monitor actually is.
    //
    // The floating two-column layout only reads correctly once the row is
    // wide enough that the text card's copy doesn't wrap onto many lines —
    // below that, a growing text card (not the min-height) drives the row
    // height while the image stays fixed at 70% width, so the box turns
    // tall and narrow and object-cover crops hard into whoever is at the
    // photo's edges. The stacked mobile layout doesn't have that problem,
    // so it's kept until `xl` (1280px) instead of switching at `lg` (1024px),
    // skipping the laptop-width range where this used to bite.
    $rowMinHClass = $wideImage ? 'xl:min-h-[min(65vh,600px)]' : 'xl:min-h-[65vh]';
@endphp

<section class="w-[93%] md:{{ $width }} mx-auto xl:my-12">
    <div class="relative {{ $rowMinHClass }} flex flex-col xl:flex-row items-stretch xl:items-center mb-12 xl:mb-20">
        {{-- Main Image - Top on mobile, Shifted right on desktop --}}
        <div class="relative xl:absolute xl:right-0 xl:top-0 w-full xl:w-[70%] h-[350px] md:h-[450px] xl:h-full rounded-[30px] overflow-hidden shadow-2xl border border-brand-gold/10 order-1 xl:order-none">
            @if($image)
                <x-ui.responsive-image
                    src="{{ $image }}"
                    alt="{{ $imageAlt }}"
                    class="w-full h-full object-cover transition-transform duration-[3000ms] hover:scale-110"
                    :sizes="$sizes"
                />
            @endif
            <div class="absolute inset-0 bg-gradient-to-r from-brand-beige-header via-transparent {{ $overlayViaClass }} to-transparent hidden xl:block"></div>
            <div class="absolute inset-0 xl:bg-brand-beige-header/20 xl:hidden backdrop-blur-[0.5px]"></div>
        </div>

        {{-- Text Content - Bottom on mobile, Floating left on desktop --}}
        <div class="relative z-10 w-full xl:w-[45%] bg-brand-beige-light xl:bg-brand-beige-light p-8 md:p-12 xl:p-16 rounded-[25px] xl:rounded-r-[25px] shadow-2xl xl:shadow-[-20px_0_50px_rgba(0,0,0,0.1)] border border-brand-gold/5 -mt-12 xl:mt-0 order-2 xl:order-none">
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
