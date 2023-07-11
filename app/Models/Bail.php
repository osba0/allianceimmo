<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bail extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
     /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'bail_local' => 'array'
    ];
    protected $fillable = [
        'bail_id',
        'bail_bien',
        'bail_type', // Habitat / commerciale
        'bail_local',
        'bail_proprio',
        'bail_etat',
        'bail_duree_contrat',
        'bail_montant_ht',
        'bail_caution_mnt_ht',
        'bail_frais_retard',
        'bail_date_debut',
        'bail_date_fin',
        'bail_depot_garantie',
        'bail_garant',
        'bail_user',
        'bail_fichiers',
        'bail_reserve_1',
        'bail_reserve_2',
        'bail_reserve_3',
        'bail_reserve_4',
        'bail_reserve_5'
    ];
}
