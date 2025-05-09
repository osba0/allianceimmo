<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Proprietaires;
use App\Models\Agence;
use App\Models\Personnels;
use App\Models\Biens;
use App\Models\MandatGerance;
use App\Models\Locataires;
use App\Models\Local;
use App\Models\Bail;
use App\Models\MailEnAttente;
use App\Models\Operations;

use App\Helpers\Helper;
use App\Http\Resources\BailResource;
use App\Services\OperationService;

use Carbon\Carbon;

use DB;
use File;

class BailController extends Controller
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

        // Get Locataires
        $listToutLocataires = Locataires::get();
        
        // Get locataire
        $locatairesSansBail  =  DB::table('locataires')
        ->leftJoin('bails', 'locataires.locat_id', '=', 'bails.bail_locataire')
        ->whereNull('bails.bail_locataire')
        ->select('locataires.*')
        ->get();


        $data = ['proprietaires' => $listProprio, 'listLocataire' => $locatairesSansBail, 'listToutLocataires' => $listToutLocataires];

        return view('bail/index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = Auth::user();
        try{
            $bail =  Bail::where('bail_id', request('id_bail'))->first();


            if($bail){

                $base64_pdf = trim(request('file_genered'), "data:application/pdf;base64,");
                $base64_decode = base64_decode($base64_pdf);
                $pathFile = config('constants.PATH_BAIL').request('name_file');
                $pdf = fopen($pathFile, 'w');
                fwrite($pdf, $base64_decode);
                fclose($pdf);

                $up = $bail->update([
                    "bail_fichiers"  => $pathFile
                ]);

                // Preparer le mail
                $data['location_date_debut'] = request('date_debut');
                $data['location_date_fin'] = request('date_fin');
                $data['montant_loyer'] = request('montant_loyer');
                $data['created_by'] = $user->username;
                $data['bien_id'] = request('bien_id');
                $data['duree_bail'] = request('duree');
                $data['locataire_id'] = request('locataire_id');
                $data['agence_id'] = auth()->user()->agence_id;
                $data['localIds'] = request('local');
                $data['path_bail'] = $pathFile;
                $sent = MailEnAttente::create([
                    'email_destinataire' => request('locataire_email'),
                    'email_cc' => "",
                    'sujet' => 'Bail de location',
                    'action'=> 'Nouveau bail de location',
                    'contenu_html' => '',
                    'contenu_text' => '',
                    'fichier_joint' => $pathFile,
                    'etat' => 'en_attente',
                    'template' => 'nouveau_bail_location',
                    'data' => json_encode($data)
                ]);

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

        }catch(\Exceptions $e){
              return response([
                "code" => 1,
                "message" => $e->getMessage()
            ]);
        }

        return response($rep);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        try{

            $q = new Bail;

            $bail_id = Helper::IDGenerator(new Bail, 'bail_id',config('constants.ID_LENGTH'), config('constants.PREFIX_BAIL'));

            $localIds = json_decode(request('local')); // Récupérer les local_id depuis la requête
            // rendre indispo le local

            DB::table('locals')->whereIn('local_id', $localIds) // Filtrer les locaux par les IDs récupérés
            ->update(['local_disponible' => false]);

            $q->bail_id = $bail_id;
            $q->bail_proprio = request('proprio');
            $q->bail_bien = request('bien');
            $q->bail_local = $localIds;
            $q->bail_type = request('type_bail');
            $q->bail_etat = 1; // true
            $q->bail_locataire = request('locataire');
            $q->bail_duree_contrat = request('duree');
            $q->bail_montant_ht = request('montant_ht');
            $q->bail_avance_loyer = request('bail_avance_loyer');
            $q->bail_user = $user->username;
            $q->bail_date_debut = request('date_debut');
            $q->bail_date_fin = request('date_fin');
            $q->bail_caution_mnt_ht = request('caution_mnt_ht');
            $q->bail_frais_retard = request('frais_retard');
            $q->bail_depot_garantie = request('depot_garantie');
            $q->bail_garant = request('depot_garantie');

            $q->save();

            // Ajouter le versement de la caution dans opération
            if(request('caution_mnt_ht') > 0){
                OperationService::enregistrer(
                    config('constants.CREDIT'),// Sens : un crédit pour la caisse
                    Operations::CAUTION, // Type : vous pouvez mettre "caution"
                    request('caution_mnt_ht'),// Montant de la caution
                    $bail_id,                 // ID du bail concerné
                    null,                     // Aucune charge liée ici
                    'Caution versée à la signature du bail', // Note explicative
                    $user->username
                );
            }

            if(request('bail_avance_loyer') > 0){
                OperationService::enregistrer(
                    config('constants.CREDIT'),// Sens : un crédit pour la caisse
                    Operations::AVANCE_LOYER, // Type : vous pouvez mettre "avance loyer"
                    request('bail_avance_loyer'),// Montant de la caution
                    $bail_id,                 // ID du bail concerné
                    null,                     // Aucune charge liée ici
                    'Avance loyer à la signature du bail', // Note explicative
                    $user->username
                );
            }






        }catch(\Exceptions $e){
              return response([
                "code" => 1,
                "message" => $e->getMessage()
            ]);
        }

        return response([
            "code" => 0,
            "id_bail" => $bail_id,
            "message" => "OK"
        ]);
    }

     /**
     * Listing
     *
     * @return \Illuminate\Http\Response
     */
    public function listing()
    {
        $user = Auth::user();

        $paginate = request('paginate');  

        $bails = Bail::leftJoin('proprietaires', 'bails.bail_proprio', '=', 'proprietaires.proprio_id')
        ->leftJoin('locataires', 'bails.bail_locataire', '=', 'locataires.locat_id')
        ->select('bails.*','locataires.locat_id','locataires.locat_email','locataires.locat_nom', 'locataires.locat_prenom', 'proprietaires.proprio_nom', 'proprietaires.proprio_prenom')->groupBy('bails.bail_id');

        if(request('locataireID')){

            $bails->where('locataires.locat_id', request('locataireID'));
        }
        if(isset($paginate)){
           $bails = $bails->orderby("created_at", "desc")->paginate($paginate);
        }else{
            $bails = $bails->get();
        }

      
        return BailResource::collection($bails);
    }


    public function getLocalByBien($id_bien, $id_proprio){
        $user = Auth::user();

        // Info Agence 
        $mandat = MandatGerance::where("bien", $id_bien)->where("proprio", $id_proprio)->first();

        if($mandat){
            // Details Agence
            $infosAgence = Agence::where("agence_id", $mandat['agence'])->first();

            // Details responsable
            $infosPersonnel = Personnels::where("pers_id", $mandat['pers'])->first();

            // List local
            if(request()->query('showAll')){

                $listLocals = Local::where("bien_id", $id_bien)->get();
            }else{
                $listLocals = Local::where("bien_id", $id_bien)->where("local_disponible", true)->get();
            }


             return response([
                "code"        => 0,
                "locaux"     => $listLocals,
                "agence"      => $infosAgence,
                "responsable" => $infosPersonnel
            ]); 

        }else{
            return response([
                "code" => 1,
                "message" => "Aucun Mandat de Gérance n'a été crée."
            ]); 
        }
        
    }

     public function getBienByProprio($id){
        $id_bien_exist = DB::table('mandat_gerances')->select('mandat_gerances.*')->where('proprio', '=', $id)->get()->toArray();


        foreach ($id_bien_exist as $id_bien) {
            $data[] = $id_bien->bien;
        }
       
        $listBiens = Biens::where("bien_proprio", $id)->whereIn('bien_id', $data)->get();
        if($listBiens){
            return response([
                "code" => 0,
                "data" => $listBiens
            ]); 
        }
        
    }

    public function findLocalLoue($id_locataire)
    {
        $bails = DB::table('bails')
            ->where('bail_locataire', $id_locataire)
            ->where('bail_etat', true)
            ->get();

        $localIds = [];
        $bailsByLocals = [];

        foreach ($bails as $bail) {
            $bailLocaux = json_decode($bail->bail_local, true);
            if (is_array($bailLocaux)) {
                $localIds = array_merge($localIds, $bailLocaux);
                foreach($bailLocaux as $bailLocal){
                    $bailsByLocals[] = array($bailLocal => $bail->bail_id);
                }

            }
        }

        $locaux = DB::table('locals')
            ->leftJoin('biens', 'locals.bien_id', '=', 'biens.bien_id')
            ->select(
                'locals.local_id',
                'locals.local_type_local',
                'locals.local_nature_local',
                'locals.local_photos',
                'locals.local_type_location',
                'locals.local_prix_loyer',
                'biens.bien_id',
                'biens.bien_adresse',
                'biens.bien_numero',
                'biens.bien_ville',
                'biens.bien_pays'
            )->whereIn('locals.local_id', $localIds)
            ->get();

        return response([
            "code" => 0,
            "data" => $locaux,
            "findBailByLocal" => $bailsByLocals
        ]);
    }

     public function resiliationBail(Request $request){
        $user = Auth::user();
        try{

            Bail::where('bail_id', request('identifiant'))
              ->update([
                "bail_etat" => false
            ]);

             // Preparer le mail
            $data['location_date_debut'] = request('date_debut');
            $data['location_date_fin'] = request('date_fin');
            $data['montant_loyer'] = request('montant_loyer');
            $data['created_by'] = $user->username;
            $data['bien_id'] = request('bien');
            $data['duree_bail'] = request('duree');
            $data['locataire_id'] = request('locataire_id');
            $data['agence_id'] = auth()->user()->agence_id;
            $data['localIds'] = request('local');
            $data['path_bail'] = request('pathFile');
            $data['date_resiliation'] = Carbon::now()->format('d/m/Y');
            $sent = MailEnAttente::create([
                'email_destinataire' => request('locataire_email'),
                'email_cc' => "",
                'sujet' => 'Résiliation Bail de location',
                'action'=> 'Résiliation Bail de location',
                'contenu_html' => '',
                'contenu_text' => '',
                'fichier_joint' => request('pathFile'),
                'etat' => 'en_attente',
                'template' => 'resiliation_bail_location',
                'data' => json_encode($data)
            ]);



        }catch(\Exceptions $e){
            return response([
                "code" => 1,
                "message" => $e->getMessage()
            ]);
        }

        return response([
            "code" => 0,
            "message" => "Bail resilié avec succés!",
        ]);
    }

}
