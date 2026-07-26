<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    protected $fillable = ['name', 'slug', 'status'];

    public function destinations()
    {
        return $this->hasMany(Destination::class);
    }
}
