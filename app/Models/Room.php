<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $fillable = [
        'property_id', 'title', 'description', 'bed_type', 'images', 'meals', 'is_active'
    ];

    protected $casts = [
        'images' => 'array',
        'meals' => 'array',
        'is_active' => 'boolean',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}
