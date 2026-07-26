<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Destination extends Model
{
    protected $fillable = ['name', 'slug', 'image', 'description', 'status', 'is_best_rate', 'show_on_homepage', 'latitude', 'longitude', 'show_in_menu', 'state_id'];

    protected $casts = [
        'show_in_menu' => 'boolean',
        'is_best_rate' => 'boolean',
        'show_on_homepage' => 'boolean',
    ];

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function properties(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Property::class);
    }
}
