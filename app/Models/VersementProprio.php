<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VersementProprio extends Model
{
     use HasFactory;

    protected $table = 'versements_proprietaires';

    protected $fillable = [
        'versement_id',
        'versement_proprio_id',
        'versement_bien_id',
        'versement_montant',
        'versement_type',
        'versement_description',
        'versement_date',
        'versement_moyen_paiement',
        'versement_user'
    ];

    public function proprietaire()
    {
        return $this->belongsTo(Proprietaires::class, 'versement_proprio_id', 'proprio_id');
    }
}
