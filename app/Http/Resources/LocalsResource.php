<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

use Carbon\Carbon;

class LocalsResource extends JsonResource
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
            "identifiant"        => $this->local_id,
            "type_local"         => $this->local_type_local,
            "type_location"      => $this->local_type_location,
            "prix_loyer"         => is_numeric($this->local_prix_loyer)?number_format($this->local_prix_loyer, 0, '', ' '):$this->local_prix_loyer,
            "montant_charge"     => is_numeric($this->local_montant_charge)? number_format($this->local_montant_charge, 0, '', ' '):$this->local_montant_charge,
            "superficie"         => number_format($this->local_superficie, 2, '.', ' '),
            "nombre_piece"       => $this->local_nombre_piece,
            "salle_bain"         => $this->local_salle_bain,
            "description"        => $this->local_description,
            "annee_construction" => $this->local_annee_construction,
            "annee_cons_natif"   => $this->local_annee_construction,
            "photo"              =>  (isset($this->local_photos ) && !is_null($this->local_photos ) && $this->local_photos !='')? is_array($this->local_photos )?$this->local_photos : json_decode($this->local_photos ) : json_decode("[]"),
            "date_creation"      => Carbon::parse($this->created_at)->format('d/m/Y H:i:s'),
            "date_modif"         => Carbon::parse($this->updated_at)->format('d/m/Y H:i:s'),
            "user"               => $this->user,
            // new champs
            "nature_local"       => $this->local_nature_local,
            "nbre_toilette"      => $this->local_nbre_toilette,
            "nbre_chambre"       => $this->local_nbre_chambre,
            "nbre_salle_bain"    => $this->local_nbre_salle_bain,
            "nbre_cuisine"       => $this->local_nbre_cuisine,
            "nbre_piscine"       => $this->local_nbre_piscine,
            "tom"                => $this->local_tom,
            "tva"                => $this->local_tva,
            "tlv"                => $this->local_tlv,
            "timbre_principal"   => $this->local_timbre_principal,
            "timbre"             => $this->local_timbre,
            "eau_forfait"        => $this->local_eau_forfait,
            "is_loue"            => $this->is_loue
        ];
    }
}
