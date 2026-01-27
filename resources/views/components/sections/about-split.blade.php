@props([
    'text' => null,
    'image' => null,
    'imageAlt' => '',
    'buttons' => [],
])

<section class="w-[87%] mx-auto my-12 md:my-16">
    <div class="flex flex-col lg:flex-row gap-8 lg:gap-12 items-stretch">
        <div class="lg:w-1/2 space-y-6 order-2 lg:order-1 flex flex-col">
            @if($text)
                <div class="bg-brand-beige-header/30 rounded-[15px] p-6 shadow-md border border-brand-gold/5">
                    <p class="text-neutral-600 text-base leading-relaxed">
                        {{ $text }}
                    </p>
                </div>
            @endif

            @foreach($buttons as $button)
                <a href="{{ $button['href'] }}" class="group block bg-brand-gold-light/20 hover:bg-brand-gold-light/40 p-8 rounded-[15px] transition-all duration-500 shadow-md hover:shadow-xl border border-brand-gold/5">
                    <h2 class="text-2xl text-brand-gold mb-2 group-hover:translate-x-1 transition-transform duration-500 tracking-tight font-bold">{{ $button['title'] }}</h2>
                    <p class="text-neutral-600 text-base leading-relaxed mb-4 opacity-90">{{ $button['text'] }}</p>
                    <div class="inline-flex items-center justify-center w-10 h-10 bg-white rounded-full shadow-sm group-hover:bg-brand-gold group-hover:text-white transition-all duration-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="lg:w-1/2 w-full order-1 lg:order-2">
            @if($image)
                <div class="h-full">
                    <x-ui.responsive-image 
                        src="{{ $image }}" 
                        alt="{{ $imageAlt }}" 
                        class="rounded-[15px] shadow-2xl w-full h-full object-cover" 
                    />
                </div>
            @endif
        </div>
    </div>
</section>
