<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Chatroom extends Model
{
    use HasFactory;

    protected $fillable = ['creator_id', 'name'];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'chatroom_users', 'chatroom_id', 'user_id');
    }
}
