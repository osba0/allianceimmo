<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Biens extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';
    protected $fillable = [
        'bien_id',
        'bien_proprio',
        'bien_nom',
        'bien_adresse',
        'bien_etage',
        'bien_numero',
        'bien_ville',
        'user',
        'bien_pays',
        'bien_description',
        'bien_superficie',
        'bien_annee_construction',
        'bien_photos'
    ];

  
}
