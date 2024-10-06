<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Proprietaires;
use App\Models\MandatGerance;

use App\Helpers\Helper;

use App\Http\Resources\ProprietairesResource;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

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

        $proprios = DB::table('proprietaires')->leftJoin('representants', 'proprietaires.proprio_id', '=', 'representants.repr_id_proprio')->select('proprietaires.*', DB::raw('COUNT(representants.repr_id_proprio) as nbreRespre'));

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


            $q->save();

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
