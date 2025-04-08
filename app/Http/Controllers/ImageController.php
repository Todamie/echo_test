<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\File;


class ImageController extends Controller
{
    /**
     * @OA\Post(
     *     path="/image",
     *     tags={"Image"},
     *     summary="Обработка изображения перед сохранением в бд",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"image", "width"},
     *                 @OA\Property(
     *                     property="image",
     *                     type="file",
     *                     description="Изображение для загрузки (jpeg, png, jpg, webp)",
     *                 ),
     *                 @OA\Property(
     *                     property="width",
     *                     type="integer",
     *                     description="Ширина изображения",
     *                     example=150
     *                 ),
     *                 @OA\Property(
     *                     property="height",
     *                     type="integer",
     *                     description="Высота изображения (опционально)",
     *                     example=150,
     *                     nullable=true
     *                 ),
     *                 @OA\Property(
     *                     property="method",
     *                     type="string",
     *                     description="Метод обработки изображения (resize по умолчанию или crop) (опционально)",
     *                     enum={"resize", "crop"},
     *                     nullable=true,
     *                     default="resize"
     *                 ),
     *                 @OA\Property(
     *                     property="path",
     *                     type="string",
     *                     description="Путь для сохранения изображения (по умолчанию images)",
     *                     example="images",
     *                     nullable=true,
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Успешный ответ",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Image")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Ошибка валидации",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $attributes = $request->validate([
            'image' => ['required', 'image', File::types(['jpeg', 'png', 'jpg', 'webp']), 'max:256000'],
            'width' => ['required', 'integer', 'min:1'],
            'height' => ['nullable', 'integer', 'min:1'],
            'method' => ['nullable', 'string', 'in:resize,crop'],
            'path' => ['nullable', 'string', 'regex:/^[a-zA-Z0-9_\-\/]+$/'],
        ]);

        if (!empty($attributes['path'])) {
            // Проверка выхода за пределы разрешенной директории
            if (str_contains($attributes['path'], '..')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Недопустимый путь',
                ], 422);
            }
        }

        $file = $attributes['image'];

        if (!empty($file)) {
            $thumbnailPath = $this->createThumbnail($file, $attributes['width'], $attributes['height'] ?? null, $attributes['method'] ?? 'resize', $attributes['path'] ?? 'images');
            $thumbnailFullPath = '/storage/' . $thumbnailPath;
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Изображение не загружено',
            ], 422);
        }

        // Сохраняем в базу данных путь к миниатюре
        $image = Image::create([
            'name' => $file->getClientOriginalName(),
            'link' => $thumbnailFullPath
        ]);

        return response()->json([
            'status' => 'success',
            'data' => $image,
        ], 200);
    }

    public function createThumbnail($image, $width = 150, $height = null, $method = 'resize', $path = 'images')
    {
        // Создание директории если её нет
        $fullPath = storage_path('app/public/' . $path);
        if (!file_exists($fullPath)) {
            mkdir($fullPath, 0777, true);
        }

        if ($height === null) {
            $saveProportions = true;
            $height = $height ?? $width;
        } else {
            $saveProportions = false;
        }

        $source = imagecreatefromstring(file_get_contents($image->path()));

        // Изначальные размеры
        $srcWidth = imagesx($source);
        $srcHeight = imagesy($source);

        // Создание thumbnail в зависимости от метода
        $thumbnail = imagecreatetruecolor($width, $height);

        if ($method === 'crop') {
            // Вычисляем соотношение сторон
            $srcRatio = $srcWidth / $srcHeight;
            $dstRatio = $width / $height;

            // Определяем размеры и координаты для обрезки
            if ($srcRatio > $dstRatio) {
                // Исходное изображение шире
                $cropWidth = round($srcHeight * $dstRatio);
                $cropHeight = $srcHeight;
                $cropX = round(($srcWidth - $cropWidth) / 2);
                $cropY = 0;
            } else {
                // Исходное изображение выше
                $cropWidth = $srcWidth;
                $cropHeight = round($srcWidth / $dstRatio);
                $cropX = 0;
                $cropY = round(($srcHeight - $cropHeight) / 2);
            }

            // Обрезаем и масштабируем изображение
            imagecopyresampled(
                $thumbnail,
                $source,
                0,
                0,
                $cropX,
                $cropY,
                $width,
                $height,
                $cropWidth,
                $cropHeight
            );
        } else {
            // Стандартный метод resize
            // Вычисляем новые размеры
            $ratio = min($width / $srcWidth, $height / $srcHeight);
            if ($saveProportions) {
                $newWidth = round($srcWidth * $ratio);
                $newHeight = round($srcHeight * $ratio);
            } else {
                $newWidth = round($width);
                $newHeight = round($height);
            }

            // Создание и ресайз изображения
            $thumbnail = imagecreatetruecolor($newWidth, $newHeight);
            imagecopyresampled(
                $thumbnail,
                $source,
                0,
                0,
                0,
                0,
                $newWidth,
                $newHeight,
                $srcWidth,
                $srcHeight
            );

            // Обновляем размеры для имени файла
            $width = $newWidth;
            $height = $newHeight;
        }

        $thumbnailName = 'thumb_' . time() . '_' . pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME) . '_' . $width . 'x' . $height . '.webp';
        $thumbnailPath = $path . '/' . $thumbnailName;
        $thumbnailFullPath = storage_path('app/public/' . $thumbnailPath);

        // Сохраняем thumbnail
        imagewebp($thumbnail, $thumbnailFullPath, 80);

        // Освобождаем память
        imagedestroy($thumbnail);
        imagedestroy($source);

        return $thumbnailPath;
    }

    public function convertToWebp($file)
    {

        $image = imagecreatefromstring(file_get_contents($file->path()));
        $filename = time() . '_' . pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '.webp';
        $path = 'images/' . $filename;

        imagewebp($image, storage_path('app/public/' . $path), 80);
        imagedestroy($image);

        return $path;
    }
}
