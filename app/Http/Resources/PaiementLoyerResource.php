<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

use App\Models\Locataires;
use App\Models\Local; 
use App\Models\Biens; 

use Carbon\Carbon;

class PaiementLoyerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $locataire = Locataires::where("locat_id", $this->bail_locataire)->first();

$localIDs = json_decode($this->bail_local, true);
$locals = [];

if (is_array($localIDs) && count($localIDs)) {
    $locals = Local::leftJoin('biens', 'biens.bien_id', '=', 'locals.bien_id')
        ->select('locals.*', 'bien_nom', 'bien_adresse')
        ->whereIn("local_id", $localIDs)
        ->groupBy('locals.local_id')
        ->get()
        ->toArray();
}
       
       

         return [
            "identifiant"             => $this->paiement_id,
            "paiement_bail_id"        => $this->paiement_bail_id,
            "paiement_montant"        => $this->paiement_montant,
            "paiements"               => $this->paiement_recu,
            "paiements_url_quittance" => $this->paiements_url_quittance,
            "charges"                 => 5000,
            "paiement_mois_location"  => Carbon::createFromFormat('Y-m', $this->paiement_mois_location)->format('M Y'), //,
            "paiement_echeance"       => Carbon::createFromFormat('Y-m', $this->paiement_mois_location)->startOfMonth()->format('Y-m-d'),
            "periode_du"              => Carbon::createFromFormat('Y-m', $this->paiement_mois_location)->startOfMonth()->format('Y-m-d'),
            "periode_au"              => Carbon::createFromFormat('Y-m', $this->paiement_mois_location)->endOfMonth()->format('Y-m-d'),
            "paiement_etat"           => $this->paiement_etat,
            "locat_nom"    => optional($locataire)->locat_nom,
            "locat_prenom" => optional($locataire)->locat_prenom,
            "locat_avoir"  => optional($locataire)->locat_avoir,
            "locaux"                  => $locals,
            "bail_montant_ht"         => $this->bail_montant_ht,
            "proprio_id"              => $this->bail_proprio,
            "proprio_nom"             => $this->proprio_nom,
            "proprio_prenom"          => $this->proprio_prenom,
            "bail_locataire"          => $this->bail_locataire,
            "loyer_y_m"               => $this->paiement_mois_location,
            "paiement_user"            => $this->paiement_user

        ];
    }
}
