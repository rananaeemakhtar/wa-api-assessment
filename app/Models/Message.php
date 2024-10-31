<?php

namespace App\Models;

use App\Models\Attachment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    use HasFactory;
    
    protected $fillable = ['user_id', 'chatroom_id', 'content'];

    public function attachments()
    {
        return $this->hasMany(Attachment::class);
    }

    public function user():BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function chatroom():BelongsTo
    {
        return $this->belongsTo(Chatroom::class);
    }
}
