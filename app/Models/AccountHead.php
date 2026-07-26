<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountHead extends Model
{
    protected $fillable = ['name', 'type', 'code', 'description', 'is_system', 'is_active'];

    public function journalLines()
    {
        return $this->hasMany(JournalEntryLine::class);
    }
    
    public function getBalanceAttribute()
    {
        $debits = $this->journalLines()->where('type', 'debit')->sum('amount');
        $credits = $this->journalLines()->where('type', 'credit')->sum('amount');
        
        if ($this->type === 'asset') {
            return $debits - $credits;
        } else { // liability, equity
            return $credits - $debits;
        }
    }
}
