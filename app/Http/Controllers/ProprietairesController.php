<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Proprietaires;
use App\Models\MandatGerance;
use App\Models\MailEnAttente;

use App\Helpers\Helper;

use App\Http\Resources\ProprietairesResource;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

use App\Services\NotificationService;
use Illuminate\Support\Str;

use DB;
use File;

class ProprietairesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth'); //If user is not logged in then he can't access this page

        // Vérifie les permissions
        $permissions = [
            'store' => 'AjouterProprietaire',
            'edit' => 'ModifierProprietaire',
            'destroy' => 'SupprimerProprietaire',
        ];

        foreach ($permissions as $method => $permission) {
            $this->middleware("permissionOrRoot:$permission")->only($method);
        }
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('proprio/index');
    }

    /**
     * Show all proprietaires.
     *
     * @return \Illuminate\Http\Response
     */
    public function listing()
    {
        $user = Auth::user();

        $paginate = request('paginate');

        $proprios = DB::table('proprietaires')->leftJoin('representants', 'proprietaires.proprio_id', '=', 'representants.repr_id_proprio')->leftJoin('bails', function ($join) {
        $join->on('proprietaires.proprio_id', '=', 'bails.bail_proprio')
             ->where('bails.bail_etat', true); })->leftJoin('locataires', 'locataires.locat_id', '=', 'bails.bail_locataire')
            ->select(
                'proprietaires.*',
                'locataires.locat_prenom',
                'locataires.locat_nom',
                'locataires.locat_tel_1',
                DB::raw('COUNT(DISTINCT representants.repr_id_proprio) as nbreRespre'),
                DB::raw('COUNT(DISTINCT bails.bail_id) as nbreLocatairesActifs'),
                DB::raw('COUNT(DISTINCT bails.bail_bien) as nbreBienLoue'),
                DB::raw('COALESCE(SUM(bails.bail_montant_ht), 0) as revenus_locatifs')
            );

        if(request('proprioID')){
            $proprios->where('proprietaires.proprio_id', request('proprioID'));
        }

        if(isset($paginate)){
            $proprios = $proprios->groupBy("proprietaires.proprio_id")->orderby("created_at", "desc")->paginate($paginate);
        }else{
            $proprios = $proprios->get();
        }

      
        return ProprietairesResource::collection($proprios);
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

            $q = new Proprietaires;

            $files = Helper::getFiles($request->TotalFiles, $request, config('constants.PATH_PROPRIO'), request('nom'), 'kyc');

            $proprio_id = Helper::IDGenerator(new Proprietaires, 'proprio_id',config('constants.ID_LENGTH'), config('constants.PREFIX_PROPRIO'));
      
            $q->proprio_id=$proprio_id;
            $q->proprio_nom=request('nom');
            $q->proprio_prenom=request('prenom');
            $q->proprio_profession=request('profession');
            $q->proprio_nationalite=request('nationalite');
            $q->proprio_ville_naissance=request('ville_naissance');
            $q->proprio_pays_naissance=request('pays_naissance');
            $q->proprio_date_naissance=request('date_naissance');
            $q->proprio_cp=request('cp');
            $q->proprio_email=request('email');
            $q->proprio_indicatif_1=request('indicatif1');
            $q->proprio_tel_1=request('tel1');
            $q->proprio_indicatif_2=request('indicatif2');
            $q->proprio_tel_2=request('tel2');
            $q->proprio_adresse=request('adresse');
            $q->proprio_ville=request('ville');
            $q->proprio_pays=request('pays');
            $q->user=$user->username;
            $q->proprio_compte_bancaire=request('compte_bancaire');
            $q->proprio_entreprise=request('entreprise');
            $q->proprio_type_piece=request('type_piece');
            $q->proprio_numero_piece=request('num_piece');
            $q->proprio_date_expiration=request('date_expiration');
            $q->proprio_kyc=json_encode($files);
            $q->agence_id = $user->agence_id;
            $q->filiale_id = $user->filiale_id;
            $q->token = Str::random(60);
            $q->pin = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT); // 🔒 PIN à 4 chiffres


            $q->save();

            $data['proprio_nom'] = request('nom');
            $data['proprio_prenom'] = request('prenom');
            $data['proprio_email'] = request('email');
            $data['proprio_tel'] = request('indicatif1').' '.request('tel1');
            $data['created_by'] = $user->username;
            $data['agence_id'] = auth()->user()->agence_id;

            $sent = MailEnAttente::create([
                'email_destinataire' => $user->email,
                'email_cc' => "",
                'sujet' => 'Nouveau Propriétaire',
                'action'=> 'Nouveau Propriétaire',
                'contenu_html' => '',
                'contenu_text' => '',
                'fichier_joint' => '',
                'etat' => 'en_attente',
                'template' => 'nouveau_proprietaire',
                'data' => json_encode($data)
            ]);

            // Lorsqu'un nouveau propriétaire est créé
            NotificationService::creerNotification(
                'creation',
                'Nouveau Propiétaire',
                "Un nouveau propiétaire a été ajouté.",
                [
                    'proprio_id' => $proprio_id,
                    'proprio_nom' => request('nom'),
                    'proprio_prenom' => request('prenom'),
                ]
            );


        }catch(\Exceptions $e){
              return response([
                "code" => 1,
                "message" => $e->getMessage()
            ]);
        }

        return response([
            "code" => 0,
            "message" => "OK"
        ]);
    }


     public function removePhoto(Request $request){
        try{   

            $allFileName=[];

            $files = json_decode($request->kyc);
     
            for($i=0; $i<sizeof($files); $i++){
                if($files[$i]!='' && $files[$i]!=$request->namePhoto){
                    array_push($allFileName, $files[$i]); 
                } 
            }
    
            Proprietaires::where('proprio_id', request('identifiant'))
              ->update([
                "proprio_kyc" => json_encode($allFileName)
            ]);

            // delete file

            File::delete("assets/kyc/".$request->namePhoto);
                

        }catch(\Exceptions $e){
            return response([
                "code" => 1,
                "message" => $e->getMessage()
            ]);
        }

        return response([
            "code" => 0,
            "message" => "OK",
            "file" => $allFileName
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id, Request $request)
    {
         $user = Auth::user();
        $additionalFile = request('additionalFile');
        if(isset($additionalFile)){
            $additionalFile = request('additionalFile');
        }else{
            $additionalFile = null;
        }

        $files = Helper::getFiles($request->TotalFiles, $request, config('constants.PATH_PROPRIO'), request('nom'), 'kyc', $additionalFile);

        $proprio =  Proprietaires::where('proprio_id', $id)->first(); 
         
        $up = $proprio->update([
            "proprio_nom"            => request('nom'),
            "proprio_prenom"         => request('prenom'),
            "proprio_profession"     => request('profession'),
            "proprio_nationalite"    => request('nationalite'),
            "proprio_ville_naissance"=> request('ville_naissance'),
            "proprio_pays_naissance" => request('pays_naissance'),
            "proprio_date_naissance" => request('date_naissance'),
            "proprio_email"          => request('email'),
            "proprio_indicatif_1"    => request('indicatif1'),
            "proprio_tel_1"          => request('tel1'),
            "proprio_indicatif_2"    => request('indicatif2'),
            "proprio_tel_2"          => request('tel2'),
            "proprio_adresse"        => request('adresse'),
            "proprio_ville"          => request('ville'),
            "proprio_cp"             => request('cp'),
            "proprio_pays"           => request('pays'),
            "proprio_compte_bancaire"=> request('compte_bancaire'),
            "proprio_entreprise"     => request('entreprise'),
            "proprio_type_piece"     => request('type_piece'),
            "proprio_numero_piece"   => request('num_piece'),
            "proprio_date_expiration"=> request('date_expiration'),
            "proprio_kyc"            => json_encode($files)
        ]);

         // Lorsqu'un nouveau locataire est créé


            NotificationService::creerNotification(
                "modification",
                "propriétaire modifié",
                "Le propriétaire ".request('nom')." a été modifié avec succès.",
                ['proprio_id' => $id],
                'root' // 👈 Ici tu cibles uniquement les utilisateurs ayant le rôle admin
            );
        
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $proprio =  Proprietaires::where('proprio_id',$id)->first(); 
       
        // Delete all files
        Helper::deleteFiles(config('constants.PATH_PROPRIO'), $proprio["proprio_kyc"]);

        // Supprimer les mandats associés
        $mandats = MandatGerance::where('proprio',$id)->first();

        if($mandats) $mandats->delete();

        $rem = Proprietaires::where('proprio_id',$id)->first();

        $rem->delete();

        if($rem){
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
}
