<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    protected $fillable = ['course_id', 'title', 'moves_sequence', 'move_descriptions', 'order'];

    protected $casts = [
        'move_descriptions' => 'array',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
