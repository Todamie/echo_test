@props(['title' => ''])

<p class='text-2xl font-bold'> {{ $title }} </p>
<div class='flex flex-col gap-4 border p-6 rounded-md w-1/3'>
    {{ $slot }}
</div>
