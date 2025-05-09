<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RapportProprietaireResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'proprio_nom' => $this->proprio_nom,
            'proprio_prenom' => $this->proprio_prenom,
            'bien_adresse' => $this->bien_adresse,
            'locat_nom' => $this->locat_nom,
            'locat_prenom' => $this->locat_prenom,
            'montant_total' => $this->montant_total,

        ];
    }
}
