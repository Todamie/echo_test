@props(['href' => '#','title' => '', 'button' => 'Поиск', 'search' => ''])

<div class='flex flex-col gap-4 w-1/4'>

    <div class='flex gap-4 items-center'>
        <span class='w-2 h-2 bg-black rounded-full'></span>
        <label for="search_article">{{ $title }}</label>
    </div>

    <div class='flex gap-4'>
        {{ $slot }}
        <a id="search-link" href="{{ $href }}" class='cursor-pointer'>{{ $button }}</a>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const input = document.querySelector('input[name="{{ $search }}"]');
        const link = document.getElementById('search-link');

        input.addEventListener('input', function() {
            link.href = "{{ $href }}/" + encodeURIComponent(input.value);
            console.log(link.href);
        });
    });
</script>