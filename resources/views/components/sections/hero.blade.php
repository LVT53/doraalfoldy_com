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
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
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
            {{-- Phones only: breaks up the stacked text boxes so the second thing in
                 view is a photo. From md the boxes sit side by side as before and the
                 photo stays in the service grid instead. --}}
            <div class="md:hidden relative rounded-[15px] overflow-hidden shadow-lg border border-brand-gold/5 aspect-[4/5]">
                <x-ui.responsive-image
                    src="{{ $image }}"
                    alt="{{ $imageAlt }}"
                    sizes="100vw"
                    class="absolute inset-0 w-full h-full object-cover object-[center_30%]"
                />
            </div>
        @endif
        <div class="bg-brand-beige-header/50 rounded-[15px] p-6 md:p-8 shadow-lg flex items-center justify-center">
            <div class="max-w-xl">
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
    </div>
</section>
