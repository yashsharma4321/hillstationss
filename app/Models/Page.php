<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'description',
        'banner_image',
        'banner_alt_text',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'schema',
    ];

    protected $casts = [
        'schema' => 'array',
    ];

    public function detail()
    {
        return $this->hasOne(PageDetail::class);
    }
}
