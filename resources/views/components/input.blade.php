@props(['name', 'type' => 'text', 'placeholder' => '', 'label' => ''])

@if ($type == 'file')
    <div class='flex flex-col gap-1 w-full'>
        <label for="{{ $name }}" class='text-sm'>{{ $label }}</label>
        <input type="{{ $type }}" name="{{ $name }}"
            {{ $attributes->merge(['class' => 'border-b p-1 w-full file:border file:p-1 file:rounded-md file:bg-gray-200 cursor-pointer file:cursor-pointer']) }}
            placeholder="{{ $placeholder }}">
    </div>
@else
    <div class='flex flex-col gap-1 w-full'>
        <label for="{{ $name }}" class='text-sm'>{{ $label }}</label>
        <input type="{{ $type }}" name="{{ $name }}" {{ $attributes->merge(['class' => 'border-b p-1 w-full focus:outline-none']) }} placeholder="{{ $placeholder }}">
    </div>
@endif
