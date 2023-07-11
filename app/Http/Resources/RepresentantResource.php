<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

use Carbon\Carbon;

class RepresentantResource extends JsonResource
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
            "identifiant"        => $this->repr_id,
            "nom"                => $this->repr_nom,
            "prenom"             => $this->repr_prenom,
            "ind"                => $this->repr_indicatif_1,
            "tel"                => $this->repr_tel_1,
            "email"              => $this->repr_email,
            "type_piece"         => $this->repr_type_piece,
            "num_piece"          => $this->repr_numero_piece,
            "civilite"           => $this->repr_civilite,
            "user"               => $this->repr_user
        ];
    }
}
