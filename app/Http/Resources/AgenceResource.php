<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class AgenceResource extends JsonResource
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
            "id"                 => $this->id,
            "identifiant"        => $this->agence_id,
            "nom_agence"         => $this->agence_nom,
            "slogan"             => $this->agence_slogan,
            "activite"           => $this->agence_activite,
            "ninea"              => $this->agence_ninea,
            "email"              => $this->agence_email,
            "ind1"               => $this->agence_ind1,
            "tel1"               => $this->agence_tel1,
            "ind2"               => $this->agence_ind2,
            "tel2"               => $this->agence_tel2,
            "adresse"            => $this->agence_adresse,
            "ville"              => $this->agence_ville,
            "pays"               => $this->agence_pays,
            "user"               => $this->agence_user,
            "logo"               => $this->agence_logo,
            "totalFialiale"      => $this->totalFialiale,
            "agence_date_creation"      => Carbon::parse($this->created_at)->format('d/m/Y H:i:s'),
            "agence_date_modif"         => Carbon::parse($this->updated_at)->format('d/m/Y H:i:s')
        ];
    }
}
