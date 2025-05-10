<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

use Carbon\Carbon;

use DB;

class ProprietairesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $locatairesActifs = DB::table('bails')
        ->leftJoin("locataires", "locataires.locat_id", '=', 'bails.bail_locataire')
        ->select("locataires.locat_nom", "locataires.locat_prenom", "locataires.locat_tel_1", "bails.bail_montant_ht")
        ->where('bail_proprio', $this->proprio_id)->where('bails.bail_etat', true)->get()->toArray();

        $locauxLoue = DB::table('locals')
        ->join('bails', DB::raw("JSON_UNQUOTE(JSON_EXTRACT(bails.bail_local, '$[0]'))"), '=', 'locals.local_id')
        ->join('biens', 'locals.bien_id', '=', 'biens.bien_id')
        ->select("locals.*", "locals.local_id as identifiantLocal", "biens.*", "bails.*")
        ->where('bail_proprio', $this->proprio_id)->where('bails.bail_etat', true)->get()->toArray();

         return [
            "nom"                       => $this->proprio_nom,
            "prenom"                    => $this->proprio_prenom,
            "date_naissance"            => Carbon::parse($this->proprio_date_naissance)->format('d/m/Y'),
            "date_naiss_natif"          => $this->proprio_date_naissance,
            "pays_naissance"            => $this->proprio_pays_naissance,
            "identifiant"               => $this->proprio_id,
            "email"                     => $this->proprio_email,
            "adresse"                   => $this->proprio_adresse,
            "ville"                     => $this->proprio_ville,
            "profession"                => $this->proprio_profession,
            "nationalite"               => $this->proprio_nationalite,
            "ville_naissance"           => $this->proprio_ville_naissance,
            "cp"                        => $this->proprio_cp,
            "pays"                      => $this->proprio_pays,
            "ind1"                      => $this->proprio_indicatif_1,
            "tel1"                      => $this->proprio_tel_1,
            "ind2"                      => $this->proprio_indicatif_2,
            "tel2"                      => $this->proprio_tel_2,
            "auteur"                    => $this->user,
            "entreprise"                => $this->proprio_entreprise,
            "compte_bancaire"           => $this->proprio_compte_bancaire,
            "type_piece"                => $this->proprio_type_piece,
            "num_piece"                 => $this->proprio_numero_piece,
            "kyc"                       =>  (isset($this->proprio_kyc) && !is_null($this->proprio_kyc) && $this->proprio_kyc!='')? is_array($this->proprio_kyc)?$this->proprio_kyc: json_decode($this->proprio_kyc) : json_decode("[]"),
            "date_expiration_natif"     => $this->proprio_date_expiration,
            "date_expiration"           => Carbon::parse($this->proprio_date_expiration)->format('d/m/Y'),
            "date_creation"             => Carbon::parse($this->created_at)->format('d/m/Y H:i:s'),
            "date_modif"                => Carbon::parse($this->updated_at)->format('d/m/Y H:i:s'),
            "nbreRespre"                => $this->nbreRespre,
            "nbreLocatairesActifs"      => $this->nbreLocatairesActifs,
            "locataire_prenom"          => $this->locat_prenom,
            "locataire_nom"             => $this->locat_nom,
            "locataire_telephone"       => $this->locat_tel_1,
            "locatairesActifs"          => $locatairesActifs,
            "revenus_locatifs"          => $this->revenus_locatifs,
            "nbreBienLoue"              => $this->nbreBienLoue,
            "locauxloue"                => $locauxLoue,
            "pin"                       => $this->pin,

        ];
    }
}
