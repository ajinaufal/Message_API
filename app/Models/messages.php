<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class messages extends Model
{
    use HasFactory;
    protected $table = 'messages';
    protected $fillable = [
        'id',
        'message',
        'seen',
        'deleted',
        'conversation_id',
        'user_id',
        'updated_at',
        'created_at',
    ];
    public function get_user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function get_conversation()
    {
        return $this->belongsTo(conversations::class, 'conversation_id');
    }
}
