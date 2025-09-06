<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = ['type'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'conversation_members')
                    ->withPivot('joined_at', 'last_seen_at', 'unread_count')
                    ->withTimestamps();
    }

    public function members()
    {
        return $this->hasMany(ConversationMember::class);
    }

    public function messages()
    {
        return $this->hasMany(Msg::class);
    }
}