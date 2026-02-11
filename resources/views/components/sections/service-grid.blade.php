@props([
    'items' => [],
])

<section class="site-container my-12">
    <div id="service-masonry" class="masonry-grid-container -mx-3 flex flex-col md:block">
        <!-- Gutter sizer for spacing -->
        <div class="masonry-column-item hidden md:block w-full md:w-1/2 lg:w-1/4 px-3 pointer-events-none opacity-0 h-0"></div>

        @foreach($items as $item)
            <div class="masonry-column-item w-full md:w-1/2 lg:w-1/4 px-3 mb-6">
                <div class="{{ $item['type'] === 'text' ? ($item['color'] ?? 'bg-brand-beige-header/45') : '' }} rounded-[15px] overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-500 {{ $item['type'] === 'text' ? 'p-8 md:p-10' : '' }} border border-brand-gold/5">
                    @if($item['type'] === 'text')
                        @if(isset($item['href']))
                            <a href="{{ $item['href'] }}" class="inline-block group/link mb-6">
                                <h3 class="text-xl md:text-2xl font-serif font-bold text-neutral-900 group-hover/link:text-brand-gold transition-colors">{{ $item['title'] }}</h3>
                                <div class="w-8 h-0.5 bg-brand-gold mt-2 group-hover/link:w-16 transition-all"></div>
                            </a>
                        @else
                            <h3 class="text-xl md:text-2xl font-serif font-bold text-neutral-900 mb-6">{{ $item['title'] }}</h3>
                        @endif
                        <p class="header-para leading-relaxed">
                            {{ $item['text'] }}
                        </p>
                    @else
                        <x-ui.responsive-image
                            src="{{ $item['src'] }}"
                            alt="{{ $item['alt'] ?? '' }}"
                            class="w-full h-auto object-cover"
                        />
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</section>
