<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RapportAgenceResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'paiement_id' => $this->paiement_id,
            'paiement_recu' => $this->paiement_recu,
            'paiement_montant' => $this->paiement_montant,
            'paiement_mois_location' => $this->paiement_mois_location,
            'paiement_etat' => $this->paiement_etat,
            'paiement_bail_id' => $this->paiement_bail_id,
            'bien_nom' => $this->bien_nom,
            'bien_adresse' => $this->bien_adresse,
            'bail_montant_ht' => $this->bail_montant_ht,
            'locat_nom' => $this->locat_nom,
            'locat_prenom' => $this->locat_prenom,
            'locat_id' => $this->locat_id,
        ];
    }
}
