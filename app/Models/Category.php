<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kalnoy\Nestedset\NodeTrait;
use Illuminate\Support\Str;

/**
 * @OA\Schema(
 *     title="Category",
 *     description="Модель категории",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Категория 1"),
 *     @OA\Property(property="image", type="string", example="https://dummyimage.com/600x400/5e5887/9dbefa&text=non"),
 *     @OA\Property(property="description", type="string", example="Описание категории 1"),
 *     @OA\Property(property="slug", type="string", example="kategoriya-1"),
 *     @OA\Property(property="created_at", type="timestamp", example="2000-01-01 00:00:00"),
 *     @OA\Property(property="updated_at", type="timestamp", example="2000-01-01 00:00:00")
 * )
 */
class Category extends Model
{
    /** @use HasFactory<\Database\Factories\CategoryFactory> */
    use HasFactory, NodeTrait;

    protected $fillable = ['name', 'image', 'description', 'slug'];
    //Если нужно скрыть поля дерева
    // protected $hidden = ['_lft', '_rgt', 'parent_id'];
    protected $hidden = ['_lft', '_rgt'];
    
    public function articles()
    {
        return $this->belongsToMany(Article::class);
    }
    
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($category) {
            $baseSlug = Str::slug($category->name);

            $slug = $baseSlug;
            $counter = 1;
            
            // Если есть то добавляет ivanovii1 и т.д
            while (static::where('slug', $slug)->exists()) {
                $slug = $baseSlug . $counter;
                $counter++;
            }
            
            $category->slug = $slug;
        });
    }
}
