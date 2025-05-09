<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class BailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $toDate = Carbon::parse($this->bail_date_debut);
        $fromDate = Carbon::parse($this->bail_date_fin);
        $today    = Carbon::now()->format('d-m-Y');

         return [
            "identifiant"           => $this->bail_id,
            "bail_duree"            => $this->bail_duree,
            "bail_type"             => $this->bail_type,
            "bail_date_debut"       => Carbon::parse($this->bail_date_debut)->format('d/m/Y'),
            "bail_date_fin"         => Carbon::parse($this->bail_date_fin)->format('d/m/Y'),
            "bail_fichier"          => $this->bail_fichiers,
            "proprio_nom"           => $this->proprio_nom,
            "proprio_prenom"        => $this->proprio_prenom,
            "bail_etat"             => $this->bail_etat,
            "locataire_nom"         => $this->locat_nom,
            "locataire_prenom"      => $this->locat_prenom,
            "bail_difference"       => $toDate->diffInDays($fromDate),
            "bail_expiration"       => $fromDate->diffInDays($today),
            "bail_local"            => (isset($this->bail_local) && !is_null($this->bail_local) && $this->bail_local!='')? is_array($this->bail_local)?$this->bail_local: json_decode($this->bail_local) : json_decode("[]") ,
            "bail_bien"             => $this->bail_bien,
            "bail_montant_ht"       => $this->bail_montant_ht,
            "locataire_id"          => $this->locat_id,
            "locataire_email"       => $this->locat_email,
        ];
    }
}
