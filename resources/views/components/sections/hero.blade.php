@props([
    'title' => null,
    'subtitle' => null,
    'text' => null,
    'secondaryTitle' => null,
    'secondaryText' => null,
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
