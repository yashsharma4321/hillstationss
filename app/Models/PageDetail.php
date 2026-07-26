<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageDetail extends Model
{
    protected $fillable = [
        'page_id',
        'json_data',
    ];

    protected $casts = [
        'json_data' => 'array',
    ];

    public function page()
    {
        return $this->belongsTo(Page::class);
    }
}
