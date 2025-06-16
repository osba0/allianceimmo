<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Operations extends Model
{
    use HasFactory;

    const PAIEMENT_LOYER = 0;
    const AVOIR = 1;
    const EAU = 2;
    const ELECTRICITE = 3;
    const REFECTION = 4;
    const CAUTION = 6;
    const AVANCE_LOYER = 7;
    const AUTRE = 5;
    const VERSEMENT_PROPRIO = 8;


    protected $primaryKey = 'id';
    protected $fillable = [
        'oper_id',
        'oper_sens',
        'oper_type',
        'oper_type_autre',
        'oper_montant',
        'oper_id_bail',
        'oper_note',
        'oper_user',
        'oper_id_versement_proprio',
        'oper_statut',
        'oper_motif_rejet',
        'oper_date_validation'
    ];

    public static function getListCharges(){
        return [
            static::EAU  => 'Eau',
            static::ELECTRICITE   => 'Electricité',
            static::REFECTION => 'Réfection',
            static::AUTRE => 'Autre'
        ];
    }

    public static function getType($type){
        $label="";
        switch($type){
            case 0: $label="Paimement Loyer";
            break;
            case 1: $label="Avoir";
            break;
            case 2: $label="Eau";
            break;
            case 3: $label="Electricité";
            break;
            case 4: $label="Réfection";
            break;
            case 6: $label="Caution";
            break;
            case 7: $label="Avance loyer";
            break;
            case 8: $label="Versement Propriétaire";
            break;
            case 5: $label="Autre";
            break;
            default:
            $label="Type non défini";
        }

        return $label;
    }
}
