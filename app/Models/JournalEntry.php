<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JournalEntry extends Model
{
    protected $fillable = ['transaction_date', 'reference_type', 'reference_id', 'description'];

    public function lines()
    {
        return $this->hasMany(JournalEntryLine::class);
    }

    public function reference()
    {
        return $this->morphTo();
    }
}
