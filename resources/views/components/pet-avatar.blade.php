@props(['type' => 'dog', 'size' => 'md'])

@php
$sizes = [
    'sm' => 'w-12 h-12',
    'md' => 'w-20 h-20',
    'lg' => 'w-32 h-32',
    'xl' => 'w-48 h-48',
];

$avatars = [
    'dog' => '🐕',
    'cat' => '🐱',
    'dragon' => '🐉',
    'fox' => '🦊',
    'panda' => '🐼',
    'tiger' => '🐯',
];

$colors = [
    'dog' => 'from-amber-100 to-orange-100',
    'cat' => 'from-gray-100 to-slate-100',
    'dragon' => 'from-red-100 to-rose-100',
    'fox' => 'from-orange-100 to-amber-100',
    'panda' => 'from-gray-100 to-zinc-100',
    'tiger' => 'from-yellow-100 to-orange-100',
];

$sizeClass = $sizes[$size] ?? $sizes['md'];
$emoji = $avatars[$type] ?? '🐾';
$gradient = $colors[$type] ?? 'from-blue-100 to-indigo-100';
$textSize = match($size) {
    'sm' => 'text-2xl',
    'md' => 'text-4xl',
    'lg' => 'text-6xl',
    'xl' => 'text-8xl',
    default => 'text-4xl',
};
@endphp

<div {{ $attributes->merge(['class' => "$sizeClass rounded-full bg-gradient-to-br $gradient flex items-center justify-center shadow-lg"]) }}>
    <span class="{{ $textSize }}">{{ $emoji }}</span>
</div>
