<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConversationMember extends Model
{
    use HasFactory;

    protected $fillable = ['conversation_id', 'user_id', 'joined_at', 'last_seen_at', 'unread_count'];

    protected $casts = [
        'joined_at' => 'datetime',
        'last_seen_at' => 'datetime',
    ];

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}