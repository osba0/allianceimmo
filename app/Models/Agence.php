<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agence extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';
    protected $fillable = [
        'agence_id',
        'agence_slogan',
        'agence_activite',
        'agence_email',
        'agence_ninea',
        'agence_ind1',
        'agence_tel1',
        'agence_ind2',
        'agence_tel2',
        'agence_adresse',
        'agence_ville',
        'agence_pays',
        'agence_logo',
        'agence_user'
    ];
}
