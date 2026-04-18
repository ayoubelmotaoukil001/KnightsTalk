<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Puzzle extends Model
{
    protected $fillable = ['title', 'initial_fen', 'difficulty', 'solution', 'user_id'];

    protected $casts = [
        'solution' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
