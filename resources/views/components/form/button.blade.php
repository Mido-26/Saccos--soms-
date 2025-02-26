@props(['id' => null, 'name' => null, 'value' => null, 'type' => 'submit', 'color' => 'green', 'icon' => null, 'width' => 'full', 'disabled' => false])

<button 
    @if ($id)
        id="{{  $id }}"
    @endif
    @if ($name)
        name="{{  $name }}"
    @endif
    @if ($value)
    value="{{  $value }}"
    @endif
    type="{{ $type }}" 
    class="w-{{ $width }} bg-{{ $color }}-500 text-white font-semibold px-5 py-2 flex items-center rounded-lg 
           hover:bg-{{ $color }}-700 focus:outline-none focus:ring-2 focus:ring-{{ $color }}-500 
           {{ $disabled ?  'disabled:bg-gray-400 disabled:cursor-not-allowed' : '' }}" 
            {{ $disabled ? 'disabled' : '' }}
>
    @if ($icon)
        <i class="{{ $icon }} mr-2"></i>
    @endif
    {{ $slot }}
</button>
