<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

use Carbon\Carbon;

class BiensResource extends JsonResource
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
            "identifiant"        => $this->bien_id,
            "adresse"            => $this->bien_adresse,
            "nom_immeuble"       => $this->bien_nom,
            "annee_construction" =>  Carbon::parse($this->bien_annee_construction)->format('d/m/Y'),
            "annee_cons_natif"   => $this->bien_annee_construction,
            "description"        => $this->bien_description,
            "numero"             => $this->bien_numero,
            "etage"              => $this->bien_etage,
            "totalLocal"         => $this->totalLocal,
            "pays"               => $this->bien_pays,
            "superficie"         => $this->bien_superficie,
            "ville"              => $this->bien_ville,
            "user"               => $this->user,
            "proprio"            => $this->bien_proprio,
            "proprio_nom"        => $this->proprio_nom,
            "proprio_prenom"     => $this->proprio_prenom,
            "proprio_tel"        => $this->proprio_tel_1,
            "proprio_ind"        => $this->proprio_indicatif_1,
            "photo"              =>  (isset($this->bien_photos) && !is_null($this->bien_photos) && $this->bien_photos!='')? is_array($this->bien_photos)?$this->bien_photos: json_decode($this->bien_photos) : json_decode("[]"),
            "date_creation"  => Carbon::parse($this->created_at)->format('d/m/Y H:i:s'),
            "date_modif"     => Carbon::parse($this->updated_at)->format('d/m/Y H:i:s')
        ];
    }
}
