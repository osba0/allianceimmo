<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaiementLoyersSys extends Model
{
    use HasFactory;

      protected $fillable = [
        'bail_id', 'user_id', 'montant', 'date_paiement', 'mode_paiement',
        'periode_paiement', 'statut', 'solde', 'penalite', 'reference', 'generated_by_cron'
    ];

    public function bail()
    {
        return $this->belongsTo(Bail::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
