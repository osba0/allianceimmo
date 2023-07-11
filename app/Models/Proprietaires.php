<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proprietaires extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';
    protected $fillable = [
        'proprio_id',
        'proprio_nom',
        'proprio_prenom',
        'proprio_nationalite',
        'proprio_profession',
        'proprio_date_naissance',
        'proprio_pays_naissance',
        'proprio_ville_naissance',
        'proprio_email',
        'proprio_adresse',
        'proprio_ville',
        'proprio_cp',
        'proprio_pays',
        'proprio_tel_1',
        'proprio_tel_2',
        'proprio_kyc',
        'user',
        'proprio_entreprise',
        'proprio_compte_bancaire',
        'proprio_numero_piece',
        'proprio_type_piece',
        'proprio_indicatif_1',
        'proprio_indicatif_2'
    ];


    public function mandats()
    {
      return $this->hasMany(MandatGerance::class, 'proprio');
    }
}
