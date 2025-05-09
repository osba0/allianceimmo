<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

use App\Models\Operations;

use Carbon\Carbon;

class VersementProprioResource extends JsonResource
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
            "identifiant"             => $this->versement_id,
            "montant"                 => $this->versement_montant,
            "date"                    => $this->versement_date,
            "note"                    => $this->versement_description,
            "type"                    => $this->versement_type,
            "user"                    => $this->versement_user,
            "moyen_paiement"          => $this->versement_moyen_paiement,
            "date_creation"           => Carbon::parse($this->created_at)->format('d/m/Y H:i:s'),
            "proprio_nom"             => $this->proprio_nom,
            "proprio_prenom"          => $this->proprio_prenom,
            "bien_nom"                => $this->bien_nom,
            "bien_adresse"            => $this->bien_adresse,
            "bien_numero"             => $this->bien_numero,
            "bien_ville"              => $this->bien_ville,
            "bien_pays"               => $this->bien_pays,
            "local_type"              => $this->local_type_local,
            "fichier"                     =>  (isset($this->versement_fichier) && !is_null($this->versement_fichier) && $this->versement_fichier!='')? is_array($this->versement_fichier)?$this->versement_fichier: json_decode($this->versement_fichier) : json_decode("[]"),

        ];
    }
}
