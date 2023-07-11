<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class MandatGeranceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $toDate = Carbon::parse($this->mandat_date_debut);
        $fromDate = Carbon::parse($this->mandat_date_fin);
        $today    = Carbon::now()->format('d-m-Y');

         return [
            "identifiant"             => $this->mandat_id,
            "mandat_duree"            => $this->mandat_duree,
            "mandat_position"         => $this->mandat_position,
            "mandat_date_debut"       => Carbon::parse($this->mandat_date_debut)->format('d/m/Y'),
            "mandat_date_fin"         => Carbon::parse($this->mandat_date_fin)->format('d/m/Y'),
            "mandat_fichier"          => $this->mandat_fichiers,
            "proprio_nom"             => $this->proprio_nom,
            "proprio_prenom"          => $this->proprio_prenom,
            "mandat_difference"       => $toDate->diffInDays($fromDate),
            "mandat_expiration"       => $fromDate->diffInDays($today),
            "agence"                  => $this->agence_nom,
            "bien_nom"                => $this->bien_nom,
            "bien_numero"             => $this->bien_numero,
            "bien_adresse"             => $this->bien_adresse,
            "user"                    => $this->mandat_user

        ];
    }
}
