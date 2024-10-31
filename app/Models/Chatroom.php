<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Chatroom extends Model
{
    use HasFactory;

    protected $fillable = ['creator_id', 'name'];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'chatroom_users', 'chatroom_id', 'user_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'chatroom_id', 'id');
    }
}
