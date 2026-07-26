<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JournalEntryLine extends Model
{
    protected $fillable = ['journal_entry_id', 'account_head_id', 'type', 'amount'];

    public function journalEntry()
    {
        return $this->belongsTo(JournalEntry::class);
    }

    public function accountHead()
    {
        return $this->belongsTo(AccountHead::class);
    }
}
