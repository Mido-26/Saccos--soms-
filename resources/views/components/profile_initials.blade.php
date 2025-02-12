@props([
    'initials',
    'bgColor' => 'bg-gray-500',
    'width' => 'w-10', 
    'height' => 'h-10',
    'textColor' => 'text-white',
    'fontSize' => 'text-lg',
    'link' => '#' // Default value for link
])
<div class="{{ $width }} {{ $height }} flex items-center justify-center {{ $bgColor }} {{ $textColor }} font-bold {{ $fontSize }} rounded-full">
    {{ $initials }}
</div>