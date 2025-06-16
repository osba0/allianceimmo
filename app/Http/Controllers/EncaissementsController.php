<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PaiementsLoyer;
use App\Models\Operations;
use App\Http\Resources\OperationsResource;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EncaissementsController extends Controller
{
    public function index()
    {

        return view('encaissements/index');
    }

     public function all()
    {
        $user = Auth::user();

        $paginate = request('paginate');

        $oper = $oper = Operations::leftJoin('bails', 'operations.oper_id_bail', '=', 'bails.bail_id')->leftJoin('charges_frais', 'operations.oper_id_charge', '=', 'charges_frais.charge_id')->leftJoin('versements_proprietaires', 'operations.oper_id_versement_proprio', '=', 'versements_proprietaires.versement_id')
        ->select('operations.*','versements_proprietaires.versement_proprio_id', 'bails.bail_locataire', 'bails.bail_local','bails.bail_proprio', 'charges_frais.charge_id_proprio', 'charges_frais.charge_id_bien', 'charges_frais.charge_id_local')->where("oper_statut", 'en_attente')->groupBy('operations.oper_id');
        if(isset($paginate)){
            $oper = $oper->orderby("operations.created_at", "desc")->paginate($paginate);
        }else{
            $oper = $oper->get();
        }


        return OperationsResource::collection($oper);


       /*if (isset($paginate)) {
            $paiements = PaiementsLoyer::orderBy('created_at', 'desc')->paginate($paginate);
            $paiementsEnAttente = $paiements->filter(function ($paiement) {
                return count($paiement->paiements_non_valides) > 0;
            })->values();
        } else {
            $paiementsEnAttente = PaiementLoyer::orderBy('created_at', 'desc')->get()->filter(function ($paiement) {
                return count($paiement->paiements_non_valides) > 0;
            })->values();
        }
        return response()->json([
                'code' => 0,
                'data' => $paiementsEnAttente
            ]);*/

        //return VersementProprioResource::collection($versements);
    }

    public function validation($id_operation)
    {
        $operations =  Operations::where('oper_id', $id_operation)->first();





            // update les tables concernés
            switch ($operations->oper_type) {
                case Operations::PAIEMENT_LOYER:
                    $response = $this->validerPaiementPartiel($operations->oper_reserve_3,$operations->oper_reserve_2);

                    $up = $operations->update([
                        "oper_statut" => 'valide'
                    ]);
                    return response()->json($response);
                    break;

                case Operations::VERSEMENT_PROPRIO:
                var_dump("expression"); die();
                break;

                case Operations::AVANCE_LOYER:
                var_dump("expression"); die();
                break;

                case Operations::CAUTION:
                var_dump("expression"); die();
                break;

                case Operations::REFECTION:
                var_dump("expression"); die();
                break;

                case Operations::REFECTION:
                var_dump("expression"); die();
                break;

                default:
                    // code...
                    break;
            }




    }

    public function validerPaiementPartiel($paiementId, $reference)
    {
        // 1. Récupérer la ligne
        $paiement = DB::table('paiements_loyers')->where('paiement_id', $paiementId)->first();

        if (!$paiement) {
            return response()->json(['code' => 1, 'message' => 'Paiement introuvable.']);
        }

        // 2. Parser le JSON
        $paiementsRecus = json_decode($paiement->paiement_recu, true);

        // 3. Rechercher et mettre à jour le paiement correspondant
        $updated = false;

        $montantMnt = 0;
        foreach ($paiementsRecus as &$item) {

            if ($item['reference'] === $reference) {
                $item['validate'] = true;
                $updated = true;
                $montantMnt += $item['paiementMontant'];
                break;
            }else{
                $montantMnt += $item['paiementMontant'];
            }
        }

        if (!$updated) {
            return response()->json(['code' => 1, 'message' => 'Référence non trouvée.']);
        }

        // 4. Enregistrer en base
        DB::table('paiements_loyers')
            ->where('paiement_id', $paiementId)
            ->update(['paiement_recu' => json_encode($paiementsRecus),'paiement_etat' => ($montantMnt >= $paiement->paiement_montant ? PaiementsLoyer::PAYE:PaiementsLoyer::PAIEMENT_PARTIEL), 'updated_at' => now()]);

        return ['code' => 0, 'message' => 'Paiement validé avec succès.'];
    }
}
