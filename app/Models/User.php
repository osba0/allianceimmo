<?php

namespace App\Models;

use App\Models\blog\Post;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'photo',
        'username',
        'password',
        'is_admin',
        'is_subscribed',
        'agence_id',
        'filiale_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function gallery()
    {
        return $this->hasMany(Gallery::class);
    }

    public function page()
    {
        return $this->hasMany(User::class);
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    // Définir la relation "un à un" avec Personnels
    public function personnel()
    {
        return $this->hasOne(Personnels::class, 'pers_user_id');
    }

    public function agence()
    {
        return $this->belongsTo(Agence::class);
    }

    public function filiale()
    {
        return $this->belongsTo(Filiale::class);
    }
}
