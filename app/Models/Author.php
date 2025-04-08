<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * @OA\Schema(
 *     title="Author",
 *     description="Модель автора",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="last_name", type="string", example="Иванов"),
 *     @OA\Property(property="first_name", type="string", example="Иван"),
 *     @OA\Property(property="middle_name", type="string", example="Иванович"),
 *     @OA\Property(property="logo", type="string", example="https://dummyimage.com/600x400/5e5887/9dbefa&text=non"),
 *     @OA\Property(property="birthdate", type="date", example="2000-01-01"),
 *     @OA\Property(property="bio", type="string", example="Биография автора"),
 *     @OA\Property(property="slug", type="string", example="ivanovii"),
 *     @OA\Property(property="created_at", type="timestamp", example="2000-01-01 00:00:00"),
 *     @OA\Property(property="updated_at", type="timestamp", example="2000-01-01 00:00:00"),
 * )
 */
class Author extends Model
{
    /** @use HasFactory<\Database\Factories\AuthorFactory> */
    use HasFactory;

    protected $fillable = ['last_name', 'first_name', 'middle_name', 'logo', 'birthdate', 'bio', 'slug'];

    protected $hidden = ['priority'];

    public function articles()
    {
        return $this->hasMany(Article::class);
    }

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($author) {
            // ivanovii
            $baseSlug = Str::slug($author->last_name . substr($author->first_name, 0, 1) . substr($author->middle_name, 0, 1));
            
            $slug = $baseSlug;
            $counter = 1;
            
            // Если есть то добавляет ivanovii1 и т.д
            while (static::where('slug', $slug)->exists()) {
                $slug = $baseSlug . $counter;
                $counter++;
            }
            
            $author->slug = $slug;
        });
    }

}
