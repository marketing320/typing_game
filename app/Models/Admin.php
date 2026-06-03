<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Admin extends Authenticatable
{
    protected $fillable = ['name', 'email', 'password', 'role', 'is_active'];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'is_active' => 'boolean',
        'password' => 'hashed',
    ];

    public function challenges(): HasMany
    {
        return $this->hasMany(TypingChallenge::class, 'created_by');
    }

    public function typingTexts(): HasMany
    {
        return $this->hasMany(TypingText::class, 'created_by');
    }
}
