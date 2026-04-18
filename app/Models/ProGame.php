<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProGame extends Model
{
    use HasFactory;

    protected $table = 'analyses';

    protected $fillable = ['title', 'moves_data'];

    protected $casts = [
        'moves_data' => 'array',
    ];
}
