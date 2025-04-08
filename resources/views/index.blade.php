<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Echo Тестовое</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class='p-10'>

    <p class='font-bold text-2xl'>Документация: <a href="/api/docs" target='_blank' class='underline underline-offset-4'>Swagger</a></p>

    <p class='text-2xl font-bold mt-10 mb-4'>Можно позапускать api ниже:</p>

    <div class='flex flex-col gap-4'>

        <x-index-border title='Авторы'>
            <x-href href="/api/authors">Список авторов</x-href>

            <x-form action="/api/authors/search" method="POST" title='Поиск автора по фамилии:'>
                <x-input name='search_author'></x-input>
            </x-form>

            <x-form id='search-author' action="/api/authors" search="idOrSlug"
                title='Поиск автора по id или slug (только точное совпадение):'>
                <x-input name='idOrSlug'></x-input>
            </x-form>
        </x-index-border>


        <x-index-border title='Категории'>
            <x-href href="/api/categories">Список категорий</x-href>

            <x-form id='search-category' action="/api/categories" search="idOrSlug"
                title='Поиск категории по id или slug (только точное совпадение):'>
                <x-input name='idOrSlug'></x-input>
            </x-form>
        </x-index-border>

        <x-index-border title='Статьи'>
            <x-href href="/api/articles">Cписок статей с их категориями и автором</x-href>

            <x-form action="/api/articles/search" method="POST" title='Поиск статьи по названию/категории/автору:'>
                <x-input name='search_article'></x-input>
            </x-form>

            <x-form id='search-article' action="/api/articles" search="idOrSlug"
                title='Поиск статьи по id или slug (только точное совпадение):'>
                <x-input name='idOrSlug'></x-input>
            </x-form>
        </x-index-border>

        <x-index-border title='Изображения'>
            <x-form action="/api/image" button='Загрузить изображение' method="POST" title='Превью изображения:'
                file="true">
                <div class='flex flex-col gap-2'>
                    <x-input name='image' type='file' label='Изображение' required></x-input>
                    <x-input name='width' type='number' placeholder='150' label='Ширина' required></x-input>
                    <x-input name='height' type='number' placeholder='150' label='Высота (опционально)'></x-input>
                    <x-input name='method' type='text' placeholder='resize (по умолчанию)/crop'
                        label='Метод (опционально)'></x-input>
                    <x-input name='path' type='text' placeholder='images (по умолчанию)'
                        label='Папка для сохранения (опционально)'></x-input>
                </div>
            </x-form>
        </x-index-border>

        @if (session('success'))
            <p class='text-green-500'>{{ session('success') }}</p>
        @endif

        @if ($errors->any())
            <p class='text-red-500'>{{ $errors->first() }}</p>
        @endif

    </div>

</body>

</html>
