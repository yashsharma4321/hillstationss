<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Collection extends Model
{
    protected $fillable = [
        'heading', 
        'slug', 
        'image', 
        'description', 
        'meta_title', 
        'meta_description', 
        'meta_keywords', 
        'meta_schema'
    ];

    public function properties(): BelongsToMany
    {
        return $this->belongsToMany(Property::class, 'property_collections');
    }
}
