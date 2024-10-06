<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class FilialeResource extends JsonResource
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
            "identifiant"             => $this->filiale_id,
            "nomFiliale"              => $this->filiale_name,
            "ind1"                    => $this->filiale_ind1,
            "tel1"                    => $this->filiale_tel1,
            "ind2"                    => $this->filiale_ind2,
            "tel2"                    => $this->filiale_tel2,
            "user"                    => $this->filiale_user,
            "date_creation"           => Carbon::parse($this->created_at)->format('d/m/Y H:i:s'),
            "email"                   => $this->filiale_email,
            "logo"                    => $this->filiale_logo,
            "ville"                   => $this->filiale_ville,
            "pays"                    => $this->filiale_pays,

        ];
    }
}
