<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FcmToken extends Model
{
    protected  = ['user_id', 'token', 'device_type'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
