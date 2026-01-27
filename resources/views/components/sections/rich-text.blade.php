@props([
    'title' => null,
    'width' => 'w-[75%]',
])

<section class="{{ $width }} mx-auto my-16 md:my-24">
    <div class="prose prose-neutral max-w-none prose-headings:font-serif prose-headings:text-brand-gold prose-a:text-brand-gold hover:prose-a:text-brand-gold-muted prose-ul:list-disc">
        @if($title)
            <h1 class="mb-12">{{ $title }}</h1>
        @endif
        
        {{ $slot }}
    </div>
</section>
