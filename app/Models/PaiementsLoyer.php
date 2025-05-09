<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaiementsLoyer extends Model
{
    use HasFactory;
    
    const EN_RETARD = 0;
    const IMPAYEE = 1;
    const PAIEMENT_PARTIEL = 2;
    const PAYE  = 3;

    protected $table = 'paiements_loyers';

    protected $casts = [
        'paiement_recu' => 'array'
    ];

    protected $fillable = [
        'paiement_id',
        'paiement_bail_id',
        'paiement_montant',
        'paiement_mois_location',
        'paiement_echeance',
        'paiement_recu',
        'paiement_etat',
        'paiement_cloture',
        'paiement_recu',
        'paiement_user',
        'paiement_date'
    ];
    

    /***
     * @return array
     */
    public static function getEtatPaiement()
    {
        return [
            static::EN_RETARD        => 'Initié', //'En retard',
            static::IMPAYEE          => 'Impayé',
            static::PAIEMENT_PARTIEL => 'Paiement Partiel',
            static::PAYE             => 'Payé'
        ];
    }
}
