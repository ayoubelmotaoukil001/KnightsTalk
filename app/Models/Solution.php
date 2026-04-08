<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Solution extends Model
{
    protected $fillable = ['puzzle_id', 'move', 'order'];

    public function puzzle()
    {
        return $this->belongsTo(Puzzle::class);
    }
}
