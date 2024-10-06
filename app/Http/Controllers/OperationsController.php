<?php

namespace App\Http\Controllers;

use App\Models\Proprietaires;
use App\Models\Agence;
use App\Models\Personnels;
use App\Models\Biens;
use App\Models\MandatGerance;
use App\Models\Locataires;
use App\Models\Local;
use App\Models\Bail;
use App\Models\PaiementsLoyer;
use App\Models\Operations;
use App\Models\ChargesFrais;

use App\Helpers\Helper;
use App\Http\Resources\OperationsResource;
use App\Http\Resources\PaiementLoyerResource;
use App\Http\Resources\ChargesResource;

use Illuminate\Support\Facades\Auth;

use DB;
use File;

use Illuminate\Http\Request;

class OperationsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth'); //If user is not logged in then he can't access this page
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Get Proprietaires
        $listProprio = Proprietaires::get();
        
        // Get locataire
        $listLocataire = Locataires::get();

        // Get Personnels
        $personnels = Personnels::get();

        // Get Agence
        $agence = Agence::get();


        $data = ['proprietaires' => $listProprio, 'listLocataire' => $listLocataire, 'personnels' => $personnels, 'agence' => $agence];

        return view('operations/index', $data);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function list()
    {
        // Get Proprietaires
        $listProprio = Proprietaires::get();
        
        // Get locataire
        $listLocataire = Locataires::get();

        // Get Personnels
        $personnels = Personnels::get();

        // Get Agence
        $agence = Agence::get();


        $data = ['proprietaires' => $listProprio, 'listLocataire' => $listLocataire, 'personnels' => $personnels, 'agence' => $agence];

        return view('operations/list', $data);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function charges()
    {
        // Get Proprietaires
        $listProprio = Proprietaires::get();
        
        // Get locataire
        $listLocataire = Locataires::get();

        // Get Personnels
        $personnels = Personnels::get();

        // Get Agence
        $agence = Agence::get();


        $data = ['proprietaires' => $listProprio, 'listLocataire' => $listLocataire, 'personnels' => $personnels, 'agence' => $agence];

        return view('operations/charge', $data);
    }

     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function listOperation()
    {
        $user = Auth::user();

        $paginate = request('paginate');  

        $oper = Operations::leftJoin('bails', 'operations.oper_id_bail', '=', 'bails.bail_id')->leftJoin('charges_frais', 'operations.oper_id_charge', '=', 'charges_frais.charge_id')
        ->select('operations.*', 'bails.bail_locataire', 'bails.bail_local','bails.bail_proprio', 'charges_frais.charge_id_proprio', 'charges_frais.charge_id_bien', 'charges_frais.charge_id_local')->groupBy('operations.oper_id');
        if(isset($paginate)){
            $oper = $oper->orderby("operations.created_at", "desc")->paginate($paginate);
        }else{
            $oper = $oper->get();
        }

      
        return OperationsResource::collection($oper);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function listingCharges()
    {
        $user = Auth::user();

        $paginate = request('paginate');  

        $charges = ChargesFrais::leftJoin('proprietaires', 'charges_frais.charge_id_proprio', '=', 'proprietaires.proprio_id')->leftJoin('biens', 'charges_frais.charge_id_bien', '=', 'biens.bien_id')->leftJoin('locals', 'charges_frais.charge_id_local', '=', 'locals.local_id')
        ->select('charges_frais.*', 'proprietaires.proprio_nom','proprietaires.proprio_prenom', 'biens.bien_nom', 'biens.bien_adresse', 'biens.bien_numero', 'locals.local_type_local')->groupBy('charges_frais.charge_id');
        if(isset($paginate)){
            $charges = $charges->orderby("charges_frais.created_at", "desc")->paginate($paginate);
        }else{
            $charges = $charges->get();
        }

      
        return ChargesResource::collection($charges);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function listingPaimentLoyer()
    {
        $user = Auth::user();

        $paginate = request('paginate');  

        $filtreLocataire = request('locataireFiltre');

        $paiement = PaiementsLoyer::leftJoin('bails', 'bails.bail_id', '=', 'paiements_loyers.paiement_bail_id')->leftJoin('proprietaires', 'bails.bail_proprio', '=', 'proprietaires.proprio_id')
        ->select('paiements_loyers.*', 'bails.bail_proprio', 'proprietaires.proprio_nom','proprietaires.proprio_prenom','bails.bail_locataire', 'bails.bail_local', 'bails.bail_montant_ht');
        if(!is_null($filtreLocataire)){
            $paiement = $paiement->where("bail_locataire",$filtreLocataire);
        }
        $paiement = $paiement->groupBy('paiements_loyers.paiement_id');
        if(isset($paginate)){
            $paiement = $paiement->orderby("paiements_loyers.created_at", "desc")->paginate($paginate);
        }else{
            $paiement = $paiement->get();
        }

      
        return PaiementLoyerResource::collection($paiement);
    }

   public function ajoutOperation(Request $request){
  
    $user = Auth::user();

    $paiement_list = json_decode($request->paiements);
    
    $calul_montant = 0;
    $montant_recu = 0;

    $has_avoir = "";

    if(request('avoir') > 0){
        $has_avoir =' - Avoir ('.request('avoir').')';
    }
    
    foreach($paiement_list as $paiement){
        $montant_recu += (int) $paiement->paiementMontant;
        if(!$paiement->validate){
            $calul_montant += (int) $paiement->paiementMontant;
            $paiement->validate = true;
        }
    }
    // Update paiement
    $up = PaiementsLoyer::where('paiement_id', request('id_loyer'))
              ->update([
                "paiement_recu" => $paiement_list,
                "paiement_etat" => ($montant_recu >= $request->montant_loyer ? PaiementsLoyer::PAYE:PaiementsLoyer::PAIEMENT_PARTIEL)
          ]);

    // update Avoir Locataire
    $locat = Locataires::where('locat_id', request('id_locataire'))
              ->update([
                "locat_avoir" => request('avoir')
          ]);

    // Save opération
        try{

            $q = new Operations;

            $oper_id = Helper::IDGenerator(new Operations, 'oper_id',config('constants.ID_LENGTH'), config('constants.PREFIX_OPERATION'));
      
            $q->oper_id=$oper_id;
            $q->oper_sens=config('constants.CREDIT');
            $q->oper_type=Operations::PAIEMENT_LOYER;
            $q->oper_note=($montant_recu >= $request->montant_loyer ? PaiementsLoyer::getEtatPaiement()[PaiementsLoyer::PAYE]: PaiementsLoyer::getEtatPaiement()[PaiementsLoyer::PAIEMENT_PARTIEL]).$has_avoir;
            $q->oper_montant=$calul_montant;
            $q->oper_id_bail=request('id_bail');
            $q->oper_user=$user->username;


            $q->save();

        }catch(\Exceptions $e){
              return response([
                "code" => 1,
                "message" => $e->getMessage()
            ]);
        }

        if($up){
            $rep = [
                "code" => 0,
                "message" => "OK"
            ];
        }else{
            $rep = [
                "code" => 1,
                "message" => "KO"
            ];
        }

        return response($rep);

    }

    public function ajoutCharge(Request $request){
  
    $user = Auth::user();

     // Save charge
        try{

            $c = new ChargesFrais;

            $charge_id = Helper::IDGenerator(new ChargesFrais, 'charge_id',config('constants.ID_LENGTH'), config('constants.PREFIX_CHARGES_FRAIS'));

            $files = Helper::getFiles($request->TotalFiles, $request, config('constants.PATH_FACTURE'), request('proprio'),  config('constants.PREFIX_CHARGES_FRAIS'));
      
            $c->charge_id=$charge_id;
            $c->charge_type=request('type');
            $c->charge_type_autre=request('type_autre');
            $c->charge_note=request('note');
            $c->charge_montant=request('montant');
            $c->charge_id_proprio=request('proprio');
            $c->charge_id_bien=request('bien');
            $c->charge_id_local=request('local'); 
            $c->charge_user=$user->username;
            $c->charge_facture=json_encode($files);


            $c->save();

            // Save dans Operation

            $q = new Operations;

            $oper_id = Helper::IDGenerator(new Operations, 'oper_id',config('constants.ID_LENGTH'), config('constants.PREFIX_OPERATION'));
      
            $q->oper_id=$oper_id;
            $q->oper_sens=config('constants.DEBIT');
            $q->oper_type=request('type');
            $q->oper_note=request('note');
            $q->oper_montant=request('montant');
            $q->oper_id_charge=$charge_id;
            $q->oper_user=$user->username;


            $q->save();



        }catch(\Exceptions $e){
              return response([
                "code" => 1,
                "message" => $e->getMessage()
            ]);
        }

        if($c){
            $rep = [
                "code" => 0,
                "message" => "OK"
            ];
        }else{
            $rep = [
                "code" => 1,
                "message" => "KO"
            ];
        }

        return response($rep);

    }

    public function getBienByProprio($id){
               
        $listBiens = Biens::where("bien_proprio", $id)->get();
        if($listBiens){
            return response([
                "code" => 0,
                "data" => $listBiens
            ]); 
        }
        
    }
}
