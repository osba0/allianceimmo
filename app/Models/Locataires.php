<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Locataires extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';
    protected $fillable = [
        'locat_id',
        'locat_type',
        'locat_civilite',
        'locat_nom',
        'locat_prenom',
        'locat_email',
        'locat_societe',
        'locat_indicatif_1',
        'locat_adresse',
        'locat_tel_1',
        'locat_indicatif_2',
        'locat_tel_2',
        'locat_numero_piece',
        'locat_photo_piece',
        'locat_justicatif_revenu',
        'locat_type_piece',
        'locat_date_expiration',
        'locat_photo_piece',
        'locat_photo_perso',
        'locat_user',
        'locat_profession',
        'locat_region',
        'locat_ville',
        'locat_pays',
        'locat_pays_naissance',
        'locat_date_naissance',
        'locat_code_postal',
        'locat_domaine_activite',
        'locat_ninea_rc',
        'locat_num_tva',
        'locat_avoir',
        'token',
        'pin'
    ];

    public function agence()
    {
        return $this->belongsTo(Agence::class);
    }
}
