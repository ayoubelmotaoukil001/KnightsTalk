<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'is_admin', 'profile_photo'];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'is_admin' => 'boolean',
    ];

    public function progress()
    {
        return $this->hasMany(Progress::class);
    }

    public function analyses()
    {
        return $this->hasMany(Analysis::class);
    }

    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function puzzles()
    {
        return $this->hasMany(Puzzle::class);
    }
}
