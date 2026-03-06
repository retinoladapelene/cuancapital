{{--
    Universal Card Component
    Usage:
      <x-card>...</x-card>
      <x-card variant="glass" title="Judul Panel">...</x-card>
      <x-card variant="sm" class="mt-4">...</x-card>

    Props:
      variant : 'default' | 'glass' | 'sm'   (default: 'default')
      title   : optional heading string
      class   : extra Tailwind/CSS classes to merge
--}}
@props([
    'variant' => 'default',
    'title'   => null,
])

@php
    $cardClass = match($variant) {
        'glass' => 'm-card m-card-glass',
        'sm'    => 'm-card m-card-compact',
        default => 'm-card',
    };
@endphp

<div {{ $attributes->merge(['class' => $cardClass]) }}>
    @if($title)
        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">{{ $title }}</p>
    @endif
    {{ $slot }}
</div>
