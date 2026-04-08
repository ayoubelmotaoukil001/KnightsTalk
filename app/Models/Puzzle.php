<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Puzzle extends Model
{
    protected $fillable = ['name', 'description', 'image', 'user_id', 'fen'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function solutions()
    {
        return $this->hasMany(Solution::class)->orderBy('order', 'asc');
    }
}
