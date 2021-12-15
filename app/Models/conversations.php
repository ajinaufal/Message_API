<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class conversations extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'from',
        'to',
        'updated_at',
        'created_at',
    ];
    public function get_user_from()
    {
        return $this->belongsTo(User::class, 'from');
    }
    public function get_user_to()
    {
        return $this->belongsTo(User::class, 'to');
    }
    public function get_message()
    {
        return $this->hasMany(messages::class,'conversation_id');
    }
}
