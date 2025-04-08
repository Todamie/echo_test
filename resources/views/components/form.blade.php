@props([
    'action' => '#',
    'title' => '',
    'file' => false,
    'button' => 'Поиск',
    'method' => 'GET',
    'search' => '',
    'id' => '',
])

@if ($search)
    @php
        $action = $action . '/' . request("$search");
    @endphp

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('{{ $id }}');

            form.addEventListener('submit', function(e) {
                e.preventDefault();

                let query = form.querySelector('input[name="idOrSlug"]').value;

                let newUrl = form.action + query;

                window.location.href = newUrl;
            });
        });
    </script>
@endif

<form id='{{ $id }}' action="{{ $action }}" method="{{ $method }}" class='flex flex-col gap-4 w-full'
    @if ($file) enctype="multipart/form-data" @endif>
    @if ($method == 'POST')
        @csrf
    @endif
    <div class='flex gap-4 items-center'>
        <span class='w-2 h-2 bg-black rounded-full'></span>
        <label for="search_article">{{ $title }}</label>
    </div>

    <div class='flex gap-4'>
        {{ $slot }}
        <button type="submit" class='cursor-pointer'>{{ $button }}</button>
    </div>
</form>
