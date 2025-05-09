<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RapportLocataireResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $paiements = json_decode($this->paiement_recu, true) ?? [];

        $total_paye = collect($paiements)->sum('paiementMontant');
        return [
            "paiement_id"  => $this->paiement_id,
            'total_paye' => $total_paye,
            "paiement_recu"  => $this->paiement_recu,
            "paiement_mois_location"  => $this->paiement_mois_location,
            "paiement_montant"  => $this->paiement_montant,
            "paiement_etat"  => $this->paiement_etat,
            "bien_adresse"  => $this->bien_adresse,
            "locat_nom"  => $this->locat_nom,
            "locat_prenom"  => $this->locat_prenom,

        ];
    }
}
