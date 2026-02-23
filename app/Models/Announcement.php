<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $fillable = [
        'title',
        'content',
        'image_url',
        'link_url',
        'is_urgent',
        'is_published',
        'scheduled_at',
        'status',
        'push_enabled',
        'target_audience',
    ];

    protected $casts = [
        'is_urgent' => 'boolean',
        'is_published' => 'boolean',
        'scheduled_at' => 'datetime',
        'push_enabled' => 'boolean',
    ];

    public function readByUsers()
    {
        return $this->hasMany(AnnouncementReadState::class);
    }
}
