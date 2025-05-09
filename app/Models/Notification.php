<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $table = 'notifications';

    protected $fillable = [
        'type',
        'titre',
        'message',
        'action_by',
        'payload',
        'target_role',
    ];

    protected $casts = [
        'payload' => 'array', // Important pour utiliser $notification->payload directement en array
    ];

    /**
     * Relations avec les utilisateurs liés à cette notification.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'notification_user')
                    ->withPivot('is_read')
                    ->withTimestamps();
    }
}
