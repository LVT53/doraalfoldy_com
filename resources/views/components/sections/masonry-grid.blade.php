@props([
    'images' => [],
])

<div x-data="{ 
    open: false, 
    currentIndex: 0, 
    images: {{ json_encode($images) }},
    touchStartX: 0,
    touchEndX: 0,
    openLightbox(index) {
        this.currentIndex = index;
        this.open = true;
        document.body.classList.add('overflow-hidden');
    },
    closeLightbox() {
        this.open = false;
        document.body.classList.remove('overflow-hidden');
    },
    next() {
        this.currentIndex = (this.currentIndex + 1) % this.images.length;
    },
    prev() {
        this.currentIndex = (this.currentIndex - 1 + this.images.length) % this.images.length;
    },
    handleTouchStart(e) {
        this.touchStartX = e.changedTouches[0].screenX;
    },
    handleTouchEnd(e) {
        this.touchEndX = e.changedTouches[0].screenX;
        this.handleSwipe();
    },
    handleSwipe() {
        if (this.touchEndX < this.touchStartX - 50) this.next();
        if (this.touchEndX > this.touchStartX + 50) this.prev();
    }
}" 
@keydown.window.escape="closeLightbox()"
@keydown.window.left="prev()"
@keydown.window.right="next()"
class="site-container my-12">
    
    <div class="masonry-grid flex flex-col md:block">
        <!-- Gutter/Column sizer -->
        <div class="masonry-sizer hidden md:block w-full md:w-1/2 lg:w-1/3 2xl:w-1/4 px-0"></div>

        @foreach($images as $index => $image)
            <div class="masonry-item w-full md:w-1/2 lg:w-1/3 2xl:w-1/4 md:px-3 mb-6">
                <div @click="openLightbox({{ $index }})" 
                     class="rounded-[15px] overflow-hidden shadow-md hover:shadow-xl transition-all duration-500 cursor-pointer border border-brand-gold/5 bg-brand-beige/10">
                    <x-ui.responsive-image 
                        src="{{ $image['src'] }}" 
                        alt="{{ $image['alt'] ?? 'Portfólió kép' }}" 
                        class="w-full h-auto object-cover" 
                        sizes="(max-width: 768px) 100vw, (max-width: 1024px) 50vw, 33vw"
                    />
                </div>
            </div>
        @endforeach
    </div>

    <!-- Lightbox Overlay -->
    <template x-teleport="body">
        <div x-show="open" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-[10001] bg-neutral-950/95 flex items-center justify-center backdrop-blur-sm cursor-pointer"
             @click="closeLightbox()"
             @touchstart="handleTouchStart($event)"
             @touchend="handleTouchEnd($event)">
            
            <!-- Close Button -->
            <button @click.stop="closeLightbox()" class="absolute top-6 right-6 text-white hover:text-brand-gold transition-colors p-2 z-[10002] cursor-pointer">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>

            <!-- Prev Button -->
            <button @click.stop="prev()" class="absolute left-4 top-1/2 -translate-y-1/2 text-white hover:text-brand-gold transition-colors p-4 hidden md:block z-[10002] cursor-pointer">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            </button>

            <!-- Next Button -->
            <button @click.stop="next()" class="absolute right-4 top-1/2 -translate-y-1/2 text-white hover:text-brand-gold transition-colors p-4 hidden md:block z-[10002] cursor-pointer">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </button>

            <!-- Main Image Container -->
            <div class="relative w-full h-full p-4 md:p-12 flex flex-col items-center justify-center pointer-events-none">
                <div class="max-w-5xl max-h-full w-full h-full flex items-center justify-center">
                    <img :src="images[currentIndex].src" 
                         class="max-w-full max-h-full object-contain shadow-2xl rounded-sm pointer-events-auto cursor-default"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         :key="currentIndex"
                         @click.stop
                    >
                </div>
                
                <!-- Counter -->
                <div class="absolute bottom-6 left-1/2 -translate-x-1/2 text-white/60 text-sm font-medium">
                    <span x-text="currentIndex + 1"></span> / <span x-text="images.length"></span>
                </div>
            </div>
        </div>
    </template>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        if (window.initMasonry) {
            window.initMasonry('.masonry-grid', '.masonry-item');
        }
    });
</script>
