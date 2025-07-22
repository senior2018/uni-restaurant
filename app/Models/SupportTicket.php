<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportTicket extends Model
{
    protected $fillable = [
        'name', 'email', 'subject', 'message', 'user_id', 'status', 'admin_response', 'is_registered'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
