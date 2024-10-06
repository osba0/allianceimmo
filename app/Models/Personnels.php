<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Personnels extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';
    protected $fillable = [
        'pers_id',
        'pers_user_id',
        'pers_nom',
        'pers_prenom',
        'pers_email',
        'pers_ind_1',
        'pers_tel_1',
        'pers_ind_2',
        'pers_tel_2',
        'pers_adress',
        'locat_tel_1',
        'pers_ville',
        'pers_pays',
        'user'
    ];

     // Définir la relation inverse "un à un" avec User
    public function user()
    {
        return $this->belongsTo(User::class, 'pers_user_id');
    }
}
