<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InAppNotification extends Model
{
    protected $fillable = ['user_id', 'title', 'body', 'type', 'data', 'read_at'];

    protected $casts = [
        'data' => 'json',
        'read_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
