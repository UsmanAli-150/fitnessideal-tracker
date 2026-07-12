@props(['active' => false])

@php
$classes = ($active ?? false)
            ? 'relative inline-flex items-center px-1 pt-1 text-sm font-semibold leading-5 text-white transition duration-150 ease-in-out after:content-[\'\'] after:absolute after:left-0 after:-bottom-px after:h-0.5 after:w-full after:bg-white after:rounded-full'
            : 'relative inline-flex items-center px-1 pt-1 text-sm font-medium leading-5 text-white/70 hover:text-white transition duration-150 ease-in-out after:content-[\'\'] after:absolute after:left-0 after:-bottom-px after:h-0.5 after:w-0 hover:after:w-full after:bg-white/80 after:rounded-full after:transition-all after:duration-300';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>