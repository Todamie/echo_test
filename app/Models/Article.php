<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * @OA\Schema(
 *     title="Article",
 *     description="Модель статьи",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="author_id", type="integer", example=1),
 *     @OA\Property(property="title", type="string", example="Статья 1"),
 *     @OA\Property(property="image", type="string", example="https://dummyimage.com/600x400/5e5887/9dbefa&text=non"),
 *     @OA\Property(property="announcement", type="string", example="Анонс статьи 1"),
 *     @OA\Property(property="content", type="string", example="Контент статьи 1"),
 *     @OA\Property(property="slug", type="string", example="statya-1"),
 *     @OA\Property(property="created_at", type="timestamp", example="2000-01-01 00:00:00"),
 *     @OA\Property(property="updated_at", type="timestamp", example="2000-01-01 00:00:00"),
 * )
 */
class Article extends Model
{
    /** @use HasFactory<\Database\Factories\ArticleFactory> */
    use HasFactory;

    protected $fillable = ['title', 'image', 'announcement', 'content', 'slug', 'author_id'];

    protected $hidden = ['priority'];

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function author()
    {
        return $this->belongsTo(Author::class);
    }

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($article) {
            $baseSlug = Str::slug($article->title);
            $slug = $baseSlug;
            $counter = 1;
            
            while (static::where('slug', $slug)->exists()) {
                $slug = $baseSlug . $counter;
                $counter++;
            }
            
            $article->slug = $slug;
        });
    }
}
