<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Analysis extends Model
{
    protected $fillable = ['user_id', 'analysis', 'title', 'media_url'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
