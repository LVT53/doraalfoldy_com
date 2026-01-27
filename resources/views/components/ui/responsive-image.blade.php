@props([
  'src' => null,
  'alt' => '',
  'srcset' => null,
  'sizes' => null,
  'class' => '',
  'loading' => 'lazy',
  'decoding' => 'async',
  'priority' => false,
  'fetchpriority' => null,
  'picture' => true,
  'width' => null,
  'height' => null,
  'allowEmpty' => false,
])

@php
  $resolvedSrcset = $srcset ?: \App\Support\ImageSrcset::from($src);
  $resolvedAvifSrcset = $srcset ? null : \App\Support\ImageSrcset::from($src, [], 'avif');
  $resolvedWebpSrcset = $srcset ? null : \App\Support\ImageSrcset::from($src, [], 'webp');
  $resolvedLoading = $loading;
  $resolvedFetchPriority = $fetchpriority;
  $usePicture = $picture && ($resolvedAvifSrcset || $resolvedWebpSrcset);

  if ($priority) {
    $resolvedLoading = 'eager';
    $resolvedFetchPriority = $resolvedFetchPriority ?: 'high';
  }
@endphp

@if($src || $allowEmpty)
  @if($usePicture)
    <picture>
      @if($resolvedAvifSrcset)
        <source type="image/avif" srcset="{{ $resolvedAvifSrcset }}" @if($sizes) sizes="{{ $sizes }}" @endif>
      @endif
      @if($resolvedWebpSrcset)
        <source type="image/webp" srcset="{{ $resolvedWebpSrcset }}" @if($sizes) sizes="{{ $sizes }}" @endif>
      @endif
      <img
        src="{{ $src }}"
        alt="{{ $alt }}"
        @if($resolvedSrcset) srcset="{{ $resolvedSrcset }}" @endif
        @if($sizes) sizes="{{ $sizes }}" @endif
        @if($resolvedLoading) loading="{{ $resolvedLoading }}" @endif
        @if($decoding) decoding="{{ $decoding }}" @endif
        @if($resolvedFetchPriority) fetchpriority="{{ $resolvedFetchPriority }}" @endif
        @if($width) width="{{ $width }}" @endif
        @if($height) height="{{ $height }}" @endif
        {{ $attributes->merge(['class' => $class]) }}
      >
    </picture>
  @else
    <img
      src="{{ $src }}"
      alt="{{ $alt }}"
      @if($resolvedSrcset) srcset="{{ $resolvedSrcset }}" @endif
      @if($sizes) sizes="{{ $sizes }}" @endif
      @if($resolvedLoading) loading="{{ $resolvedLoading }}" @endif
      @if($decoding) decoding="{{ $decoding }}" @endif
      @if($resolvedFetchPriority) fetchpriority="{{ $resolvedFetchPriority }}" @endif
      @if($width) width="{{ $width }}" @endif
      @if($height) height="{{ $height }}" @endif
      {{ $attributes->merge(['class' => $class]) }}
    >
  @endif
@endif
