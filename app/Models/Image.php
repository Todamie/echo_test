<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * @OA\Schema(
 *     title="Image",
 *     description="Модель изображения",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="imageName"),
 *     @OA\Property(property="link", type="string", example="/storage/images/thumb_1743663727_imageName_150x150.webp"),
 *     @OA\Property(property="created_at", type="timestamp", example="2000-01-01 00:00:00"),
 *     @OA\Property(property="updated_at", type="timestamp", example="2000-01-01 00:00:00")
 * )
 */
class Image extends Model
{
    protected $fillable = ['name', 'link'];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($image) {
            $baseSlug = Str::slug($image->name);

            $slug = $baseSlug;
            $counter = 1;
            
            // Если есть то добавляет ivanovii1 и т.д
            while (static::where('slug', $slug)->exists()) {
                $slug = $baseSlug . $counter;
                $counter++;
            }
            
            $image->slug = $slug;
        });
    }
}
