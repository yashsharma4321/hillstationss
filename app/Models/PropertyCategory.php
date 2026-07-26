<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PropertyCategory extends Model
{
    protected $fillable = ['name', 'slug', 'icon', 'is_best_view', 'show_in_menu', 'category_group'];

    protected $casts = [
        'is_best_view' => 'boolean',
        'show_in_menu' => 'boolean',
    ];

    public function properties(): HasMany
    {
        return $this->hasMany(Property::class, 'category_id');
    }
}
