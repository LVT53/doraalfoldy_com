@props([
    'title' => null,
    'text' => null,
    'image' => null,
    'imageAlt' => '',
    'featuresTitle' => 'Neked ajánlom, ha:',
    'features' => [],
    'priceDetails' => null,
    'buttonHref' => 'https://doraalfoldy.salonic.hu/',
    'sizes' => '(max-width: 1024px) 100vw, 40vw',
    'imagePosition' => 'object-center',
])

<div class="bg-brand-beige rounded-[30px] overflow-hidden shadow-lg flex flex-col h-full hover:shadow-2xl transition-all duration-700 group border border-brand-gold/5">
    @if($image)
        <div class="shrink-0 bg-white/10 aspect-[4/3] overflow-hidden">
            <x-ui.responsive-image
                src="{{ $image }}"
                alt="{{ $imageAlt }}"
                class="w-full h-full object-cover {{ $imagePosition }} group-hover:scale-105 transition-transform duration-700"
                :sizes="$sizes"
            />
        </div>
    @endif
    
    <div class="p-8 md:p-10 lg:p-12 flex flex-col flex-1">
        <h3 class="mb-6 uppercase tracking-tight">{{ $title }}</h3>
        
        <div class="header-para mb-8 flex-1 text-neutral-700">
            {{ $text }}
        </div>

        @if(count($features) > 0)
            <div class="mb-8 p-0 border-t border-brand-gold/10 pt-8">
                <h4 class="text-label mb-5">{{ $featuresTitle }}</h4>
                <ul class="space-y-4">
                    @foreach($features as $feature)
                        <li class="flex items-start gap-3 text-neutral-600 font-medium">
                            <svg class="w-5 h-5 text-brand-gold mt-1 shrink-0 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            {{ $feature }}
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="mt-auto">
            @if($priceDetails)
                <div class="leading-relaxed whitespace-pre-line text-neutral-600 font-medium mb-8 pt-8 border-t border-brand-gold/10">
                    {!! $priceDetails !!}
                </div>
            @endif

            @if($buttonHref)
                <a href="{{ $buttonHref }}" class="block text-center bg-brand-gold text-white px-6 py-4 rounded-full font-bold text-sm uppercase tracking-widest hover:bg-brand-gold-muted transition-all duration-300 shadow-gold">
                    Foglalj időpontot!
                </a>
            @endif
        </div>
    </div>
</div>
