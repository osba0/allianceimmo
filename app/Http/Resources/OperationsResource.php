<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

use App\Models\Proprietaires;
use App\Models\Local; 
use App\Models\Biens; 
use App\Models\Operations; 

use Carbon\Carbon;

class OperationsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $bien = Biens::where("bien_id", $this->charge_id_proprio)->first();

        $idproprio=null;

        if(!is_null($this->bail_proprio)){
            $idproprio = $this->bail_proprio;
        }

        if(!is_null($this->charge_id_proprio)){
            $idproprio = $this->charge_id_proprio;
        }

        if(!is_null($this->versement_proprio_id)){
            $idproprio = $this->versement_proprio_id;
        }



        $proprio = Proprietaires::where("proprio_id", $idproprio)->first();

         return [
            "identifiant"             => $this->oper_id,
            "sens"                    => $this->oper_sens,
            "type"                    => Operations::getType($this->oper_type),
            "type_autre"              => $this->oper_type_autre,
            "montant"                 => $this->oper_montant,
            "note"                    => $this->oper_note,
            'oper_statut'             => $this->oper_statut,
            'oper_motif_rejet'        => $this->oper_motif_rejet,
            'oper_date_validation'    => $this->oper_date_validation,
            "user"                    => $this->oper_user,
            "date_creation"           => Carbon::parse($this->created_at)->format('d/m/Y H:i:s'),
            "proprio_nom"             => is_null($proprio)? '': $proprio['proprio_nom'],
            "proprio_prenom"          => is_null($proprio)? '': $proprio['proprio_prenom']

        ];
    }
}
