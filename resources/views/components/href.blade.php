@props(['href' => '#'])

<div class='flex gap-4 items-center'>
    <span class='w-2 h-2 bg-black rounded-full'></span>
    <a href="{{ $href }}" class='underline underline-offset-3'>{{ $slot }}</a>
</div>