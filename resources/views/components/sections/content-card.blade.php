@props([
    'title' => null,
    'text' => null,
    'image' => null,
    'imageAlt' => '',
    'featuresTitle' => 'Neked ajánlom, ha:',
    'features' => [],
    'priceDetails' => null,
    'reverse' => false,
    'buttonHref' => null,
    'buttonText' => 'Foglalj időpontot!',
    'width' => 'w-[87%]',
    'sizes' => '(max-width: 1024px) 100vw, 45vw',
])

<section class="w-[93%] md:{{ $width }} mx-auto my-12 md:my-16">
    <div class="bg-brand-beige rounded-[15px] overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-700 border border-brand-gold/5">
        <div class="flex flex-col {{ $reverse ? 'lg:flex-row-reverse' : 'lg:flex-row' }}">
            @if($image)
                <div class="lg:w-[45%] flex items-center justify-center p-3 lg:p-0">
                    <div class="relative w-full h-full rounded-2xl overflow-hidden shadow-lg border border-brand-gold/10 lg:rounded-none lg:border-none lg:shadow-none">
                        <x-ui.responsive-image 
                            src="{{ $image }}" 
                            alt="{{ $imageAlt }}" 
                            class="w-full h-full object-cover min-h-[400px] lg:rounded-2xl lg:border lg:border-brand-gold/10" 
                            :sizes="$sizes"
                        />
                    </div>
                </div>
            @endif
            
            <div class="{{ $image ? 'lg:w-[55%]' : 'w-full' }} p-8 md:p-12 lg:p-16 flex flex-col justify-center">
                @if($title)
                    <h2 class="mb-6 uppercase tracking-tight">{{ $title }}</h2>
                @endif
                
                <div class="header-para mb-8 whitespace-pre-line text-neutral-700">
                    {!! $text !!}
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

                @if($priceDetails)
                    <div class="header-para whitespace-pre-line text-neutral-700 mb-8 pt-8 border-t border-brand-gold/10 font-medium">
                        {!! $priceDetails !!}
                    </div>
                @endif

                @if($buttonHref)
                    <div>
                        <a href="{{ $buttonHref }}" class="inline-block bg-brand-gold text-white px-8 py-3 rounded-full font-bold text-base hover:bg-brand-gold-muted hover:scale-105 transition-all duration-300 shadow-gold">
                            {{ $buttonText }}
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
