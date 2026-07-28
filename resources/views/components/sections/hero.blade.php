@props([
    'title' => null,
    'subtitle' => null,
    'text' => null,
    'secondaryTitle' => null,
    'secondaryText' => null,
    'image' => null,
    'imageAlt' => '',
])

<section class="site-container lg:my-10 space-y-6">
    <div class="grid grid-cols-1 {{ $image ? 'lg:grid-cols-2' : '' }} gap-6">
        <div class="bg-brand-beige-header/80 rounded-[15px] p-6 md:p-8 shadow-lg flex items-center justify-center">
            <div class="max-w-xl">
                @if($title)
                    <h1 class="mb-4 leading-tight">
                        {!! $title !!}
                    </h1>
                @endif
                @if($text)
                    <p class="header-para font-medium">
                        {{ $text }}
                    </p>
                @endif
            </div>
        </div>
        @if($image)
            <div class="rounded-[15px] overflow-hidden shadow-lg border border-brand-gold/5 aspect-[4/5] sm:aspect-[16/10] lg:aspect-[5/4]">
                <x-ui.responsive-image
                    src="{{ $image }}"
                    alt="{{ $imageAlt }}"
                    sizes="(min-width: 1024px) 50vw, 100vw"
                    :priority="true"
                    class="w-full h-full object-cover object-[center_30%]"
                />
            </div>
        @endif
    </div>

    @if($secondaryTitle || $secondaryText)
        <div class="bg-brand-beige-header/50 rounded-[15px] p-6 md:p-8 shadow-lg">
            <div class="max-w-4xl">
                @if($secondaryTitle)
                    <h2 class="mb-4 text-brand-gold">
                        {{ $secondaryTitle }}
                    </h2>
                @endif
                @if($secondaryText)
                    <p class="header-para font-medium">
                        {{ $secondaryText }}
                    </p>
                @endif
            </div>
        </div>
    @endif
</section>
