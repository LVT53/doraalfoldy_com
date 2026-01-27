@props([
  'bg' => 'bg-white',
  'pad' => 'py-24',
  'container' => true,
  'containerClass' => 'container mx-auto px-4',
  'title' => null,
  'titleAlign' => 'center',
  'titleClass' => 'font-serif font-bold text-neutral-900',
  'titleSpacer' => 'mb-12',
])

<section class="{{ $pad }} {{ $bg }}">
  @if($container)
    <div class="{{ $containerClass }}">
      @if($title)
        <h2 class="{{ $titleClass }} {{ $titleAlign === 'left' ? 'text-left' : 'text-center' }} {{ $titleSpacer }} text-4xl">
          {{ $title }}
        </h2>
      @endif

      {{ $slot }}
    </div>
  @else
    @if($title)
      <h2 class="{{ $titleClass }} {{ $titleAlign === 'left' ? 'text-left' : 'text-center' }} {{ $titleSpacer }} text-4xl">
        {{ $title }}
      </h2>
    @endif

    {{ $slot }}
  @endif
</section>
