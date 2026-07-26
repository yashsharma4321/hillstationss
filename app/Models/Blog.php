<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Blog extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'description',
        'content',
        'image',
        'other_images',
        'image_alt',
        'status',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'schema'
    ];

    protected $casts = [
        'other_images' => 'array',
        'schema' => 'array'
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($blog) {
            if (empty($blog->slug)) {
                $blog->slug = Str::slug($blog->title);
            }
        });
    }
}
