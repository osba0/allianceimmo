<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

use App\Models\Operations; 

use Carbon\Carbon;

class ChargesResource extends JsonResource
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
            "identifiant"             => $this->charge_id,
            "montant"                 => $this->charge_montant,
            "note"                    => $this->charge_note,
            "type"                    => Operations::getType($this->charge_type),
            "user"                    => $this->charge_user,    
            "date_creation"           => Carbon::parse($this->created_at)->format('d/m/Y H:i:s'),
            "date"                    => $this->charge_date,
            "proprio_nom"             => $this->proprio_nom,
            "proprio_prenom"          => $this->proprio_prenom,
            "bien_nom"                => $this->bien_nom,
            "bien_adresse"            => $this->bien_adresse,
            "bien_numero"             => $this->bien_numero,
            "local_type"              => $this->local_type_local,
            "fichier"                 => (isset($this->charge_facture) && !is_null($this->charge_facture) && $this->charge_facture!='')? is_array($this->charge_facture)?$this->charge_facture: json_decode($this->charge_facture) : json_decode("[]"),

        ];
    }
}
