@props([
    'title' => null,
    'text' => null,
    'image' => null,
    'imageAlt' => '',
    'width' => 'w-[87%]',
    'sizes' => '(max-width: 1024px) 100vw, 50vw',
])

<section class="w-[93%] md:{{ $width }} mx-auto my-10">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-stretch">
        <div class="relative h-80 md:h-96 lg:h-auto rounded-[15px] overflow-hidden shadow-lg border border-brand-gold/5">
            @if($image)
                <x-ui.responsive-image 
                    src="{{ $image }}" 
                    alt="{{ $imageAlt }}" 
                    class="absolute inset-0 w-full h-full object-cover" 
                    :sizes="$sizes"
                />
            @endif
        </div>
        <div class="bg-brand-beige-header/40 p-8 md:p-12 lg:p-16 rounded-[15px] shadow-lg flex items-center border border-brand-gold/5">
            <div class="max-w-xl">
                @if($title)
                    <h1 class="mb-6">
                        {{ $title }}
                    </h1>
                @endif
                @if($text)
                    <div class="header-para whitespace-pre-line">
                        {{ $text }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
