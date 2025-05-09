<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Helpers\Helper;

use App\Models\Locataires;
use App\Models\Bail;
use App\Http\Resources\LocatairesResource;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

use DB;
use File;

class LocatairesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('locataires/index');
    }

     /**
     * Show all locataires.
     *
     * @return \Illuminate\Http\Response
     */
    public function listing()
    {
        $user = Auth::user();

        $paginate = request('paginate');

        $locataires = DB::table('locataires');

        if(request('locataireID')){

            $locataires->where('locataires.locat_id', request('locataireID'));
        }

        if(isset($paginate)){
            $locataires = $locataires->orderby("created_at", "desc")->paginate($paginate);
        }else{
            $locataires = $locataires->get(); 
        }

        return LocatairesResource::collection($locataires);
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

            $q = new Locataires;

            $filesPiece = Helper::getFiles($request->TotalFiles, $request, config('constants.PATH_LOCATAIRE'), request('type_location'), config('constants.PREFIX_LOCATAIRE'));

            $filesPerso = Helper::getFiles($request->TotalFilesPerso, $request, config('constants.PATH_LOCATAIRE'), request('type_location'), config('constants.PREFIX_LOCATAIRE'), null, 'filesPerso');

            $locat_id = Helper::IDGenerator(new Locataires, 'locat_id',config('constants.ID_LENGTH'), config('constants.PREFIX_LOCATAIRE'));
      
            $q->locat_id=$locat_id;
            $q->agence_id = $user->agence_id;
            $q->locat_civilite=request('civilite');
            $q->locat_type=request('type_location');
            $q->locat_societe=request('societe');
            $q->locat_num_tva=request('num_tva');
            $q->locat_ninea_rc=request('ninea_rc');
            $q->locat_domaine_activite=request('domaine_activite');
            $q->locat_revenu_mensuel=request('revenu_mensuel');
            $q->locat_justificatif_revenu=request('justificatif_revenu');
            $q->locat_nom=request('nom');
            $q->locat_prenom=request('prenom');
            $q->locat_pays_naissance=request('pays_naissance');
            $q->locat_date_naissance=request('date_naissance');
            $q->locat_code_postal=request('cp');
            $q->locat_email=request('email');
            $q->locat_indicatif_1=request('indicatif1');
            $q->locat_tel_1=request('tel1');
            $q->locat_indicatif_2=request('indicatif2');
            $q->locat_tel_2=request('tel2');
            $q->locat_adresse=request('adresse');
            $q->locat_region=request('region');
            $q->locat_ville=request('ville');
            $q->locat_pays=request('pays');
            $q->locat_user=$user->username;
            $q->locat_profession=request('profession');
            $q->locat_type_piece=request('type_piece');
            $q->locat_numero_piece=request('num_piece');
            $q->locat_date_expiration=request('date_expiration');
            $q->locat_photo_perso=json_encode($filesPerso);
            $q->locat_photo_piece=json_encode($filesPiece);
            $q->token = Str::random(60);
            $q->pin = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT); // 🔒 PIN à 4 chiffres

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

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id, Request $request)
    {
        // File Piéce
        $additionalFilePiece = request('additionalFilePiece');
        if(isset($additionalFilePiece)){
            $additionalFilePiece = request('additionalFilePiece');
        }else{
            $additionalFilePiece = null;
        }

        $filesPiece = Helper::getFiles($request->TotalFiles, $request, config('constants.PATH_LOCATAIRE'), request('type_location'), config('constants.PREFIX_LOCATAIRE'), $additionalFilePiece);

        // File Perso
        $additionalFilePerso = request('additionalFilePerso');
        if(isset($additionalFilePerso)){
            $additionalFilePerso = request('additionalFilePerso');
        }else{
            $additionalFilePerso = null;
        }

        $filesPerso = Helper::getFiles($request->TotalFilesPerso, $request, config('constants.PATH_LOCATAIRE'), request('type_location'), config('constants.PREFIX_LOCATAIRE'), $additionalFilePerso, 'filesPerso');


        $locat =  Locataires::where('locat_id', $id)->first(); 
         
        $up = $locat->update([
            "locat_civilite"               => request('civilite'),
            "locat_type"                   => request('type_location'),
            "locat_societe"                => request('societe'),
            "locat_num_tva"                => request('num_tva'),
            "locat_ninea_rc"               => request('ninea_rc'),
            "locat_domaine_activite"       => request('domaine_activite'),
            "locat_revenu_mensuel"         => request('revenu_mensuel'),
            "locat_justificatif_revenu"    => request('justificatif_revenu'),
            "locat_nom"                    => request('nom'),
            "locat_prenom"                 => request('prenom'),
            "locat_pays_naissance"         => request('pays_naissance'),
            "locat_date_naissance"         => request('date_naissance'),
            "locat_code_postal"            => request('cp'),
            "locat_email"                  => request('email'),
            "locat_indicatif_1"            => request('indicatif1'),
            "locat_tel_1"                  => request('tel1'),
            "locat_indicatif_2"            => request('indicatif2'),
            "locat_tel_2"                  => request('tel2'),
            "locat_adresse"                => request('adresse'),
            "locat_region"                 => request('region'),
            "locat_ville"                  => request('ville'),
            "locat_pays"                   => request('pays'),
            "locat_profession"             => request('profession'),
            "locat_type_piece"             => request('type_piece'),
            "locat_numero_piece"           => request('num_piece'),
            "locat_date_expiration"        => request('date_expiration'),
            "locat_photo_perso"            => json_encode($filesPerso),
            "locat_photo_piece"            => json_encode($filesPiece)
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

     public function removePhoto(Request $request){
        try{   

            $allFileName=[];

            $files = json_decode($request->photos);
     
            for($i=0; $i<sizeof($files); $i++){
                if($files[$i]!='' && $files[$i]!=$request->namePhoto){
                    array_push($allFileName, $files[$i]); 
                } 
            }

            if($request->type == "perso"){
                Locataires::where('locat_id', request('identifiant'))
                  ->update([
                    "locat_photo_perso" => json_encode($allFileName)
                ]);
            }else{
                Locataires::where('locat_id', request('identifiant'))
                  ->update([
                    "locat_photo_piece" => json_encode($allFileName)
                ]);
            }
    
            // delete file

            File::delete(config('constants.PATH_LOCATAIRE').$request->namePhoto);
                

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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $locataire =  Locataires::where('locat_id',$id)->first();
        if(!$locataire){
            $rep = [
                "code" => 1,
                "message" => "Locataire introuvable"
            ];
        }

        // Delete all files
        if(isset($locataire["locat_photo_piece"])){
            Helper::deleteFiles(config('constants.PATH_LOCATAIRE'), $locataire["locat_photo_piece"]);
        }
        if(isset($locataire["locat_photo_perso"])){
            Helper::deleteFiles(config('constants.PATH_LOCATAIRE'), $locataire["locat_photo_perso"]);
        }

        $rem = $locataire->delete();

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
