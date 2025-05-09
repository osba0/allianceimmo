<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VersementLocataire extends Model
{
     use HasFactory;

    protected $table = 'versements_locataires';

    protected $fillable = [
        'locataire_id',
        'montant',
        'solde_utilise',
        'solde_disponible',
        'mode_paiement',
        'date_versement',
        'statut'
    ];

    public function locataire()
    {
        return $this->belongsTo(Locataires::class, 'locataire_id', 'locat_id');
    }
}
