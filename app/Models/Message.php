<?php

namespace App\Models;

use App\Models\Attachment;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = ['user_id', 'chatroom_id', 'content'];

    public function attachments()
    {
        return $this->hasMany(Attachment::class);
    }
}
