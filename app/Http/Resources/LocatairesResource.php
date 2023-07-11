<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

use Carbon\Carbon;

use App\Helpers\Helper;

class LocatairesResource extends JsonResource
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
            "identifiant"             => $this->locat_id, 
            "civilite"                => $this->locat_civilite,
            "type_location"           => $this->locat_type,
            "nom"                     => $this->locat_nom,
            "prenom"                  => $this->locat_prenom,
            "profession"              => $this->locat_profession,
            "societe"                 => $this->locat_societe,
            "num_tva"                 => $this->locat_num_tva,
            "ninea_rc"                => $this->locat_ninea_rc,
            "prefession"              => $this->locat_prefession,
            "domaine_activite"        => $this->locat_domaine_activite,
            "revenu_mensuel"          => $this->locat_revenu_mensuel,
            "justificatif_revenu"     => $this->locat_justificatif_revenu,
            "date_naissance"          => Carbon::parse($this->locat_date_naissance)->format('d/m/Y'),
            "date_naiss_natif"        => $this->locat_date_naissance,
            "pays_naissance"          => $this->locat_pays_naissance,
            "email"                   => $this->locat_email,
            "adresse"                 => $this->locat_adresse,
            "ville"                   => $this->locat_ville,
            "region"                  => $this->locat_region,
            "cp"                      => $this->locat_code_postal,
            "pays"                    => $this->locat_pays,
            "ind1"                    => $this->locat_indicatif_1,
            "tel1"                    => $this->locat_tel_1,
            "ind2"                    => $this->locat_indicatif_2,
            "tel2"                    => $this->locat_tel_2,
            "auteur"                  => $this->locat_user,
            "type_piece"              => $this->locat_type_piece,
            "num_piece"               => $this->locat_numero_piece,
            "photo_piece"             =>  (isset($this->locat_photo_piece ) && !is_null($this->locat_photo_piece ) && $this->locat_photo_piece !='')? is_array($this->locat_photo_piece )?$this->locat_photo_piece : json_decode($this->locat_photo_piece ) : json_decode("[]"),
            "photo_perso"             =>  (isset($this->locat_photo_perso  ) && !is_null($this->locat_photo_perso  ) && $this->locat_photo_perso  !='')? is_array($this->locat_photo_perso  )?$this->locat_photo_perso  : json_decode($this->locat_photo_perso  ) : json_decode("[]"),
            "date_expiration_natif"   => $this->locat_date_expiration,
            "date_expiration"         => Carbon::parse($this->locat_date_expiration)->format('d/m/Y'),
            "date_creation"           => Carbon::parse($this->created_at)->format('d/m/Y H:i:s'),
            "date_modif"              => Carbon::parse($this->updated_at)->format('d/m/Y H:i:s')
        ];
    }
}
