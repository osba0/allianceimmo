<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Local;

use App\Helpers\Helper;

use App\Http\Resources\LocalsResource;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

use DB;
use File;

class LocalController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth'); //If user is not logged in then he can't access this page
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

            $q = new Local;

            $files = Helper::getFiles($request->TotalFiles, $request, config('constants.PATH_BIEN'), request('type_local'), 'local');

            $local_id = Helper::IDGenerator(new Local, 'local_id',config('constants.ID_LENGTH'), config('constants.PREFIX_LOCAL'));
      
            $q->local_id=$local_id;
            $q->bien_id=request('bien_id');
            $q->local_type_local=request('type_local');
            $q->local_type_location=request('type_location');
            $q->local_montant_charge=request('montant_charge');
            $q->local_prix_loyer=request('prix_loyer');
            $q->user=$user->username;
            $q->local_nombre_piece=request('nombre_piece');
            $q->local_salle_bain=request('salle_bain');
            $q->local_description=request('description');
            $q->local_superficie=request('superficie');
            $q->local_disponible=true;
            $q->local_annee_construction=request('annee_construction');
            $q->local_nature_local=request('nature_local');
            $q->local_nbre_toilette=request('nbre_toilette');
            $q->local_nbre_chambre=request('nbre_chambre');
            $q->local_nbre_salle_bain=request('nbre_salle_bain');
            $q->local_nbre_cuisine=request('nbre_cuisine');
            $q->local_nbre_piscine=request('nbre_piscine');
            $q->local_tom=request('tom');
            $q->local_tva=request('tva');
            $q->local_tlv=request('tlv');
            $q->local_timbre_principal=request('timbre_principal');
            $q->local_timbre=request('timbre');
            $q->local_eau_forfait=request('eau_forfait');

            $q->local_photos=json_encode($files); 

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

     public function listing()
    {
        $user = Auth::user();

        $paginate = request('paginate');


        //$locals = DB::table('locals')->where("bien_id", request('bien_id'));

        $locals =  DB::table('locals')->where('bien_id', request('bien_id'))
        ->select('locals.*', DB::raw('
            EXISTS (
                SELECT 1 FROM bails
                WHERE JSON_CONTAINS(bails.bail_local, JSON_QUOTE(CAST(locals.local_id AS CHAR)))
                AND bails.bail_etat = 1
            ) as is_loue
        '));

        if(isset($paginate)){
            $locals = $locals->orderby("created_at", "desc")->paginate($paginate);
        }else{
            $locals = $locals->get();
        }

      
        return LocalsResource::collection($locals);
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
        $files = Helper::getFiles($request->TotalFiles, config('constants.PATH_BIEN'), request('type_local'), 'local', $additionalFile);



        $local =  Local::where('local_id', $id)->first(); 

        $up = $local->update([
            "local_type_local"          => request('type_local'),
            "local_type_location"       => request('type_location'),
            "local_montant_charge"      => request('montant_charge'),
            "local_prix_loyer"          => request('prix_loyer'),
            "local_nombre_piece"        => request('nombre_piece'),
            "local_salle_bain"          => request('salle_bain'),
            "local_description"         => request('description'),
            "local_superficie"          => request('superficie'),
            "local_annee_construction"  => request('annee_construction'),
            "local_nature_local"        => request('nature_local'),
            "local_nbre_toilette"       => request('nbre_toilette'),
            "local_nbre_chambre"        => request('nbre_chambre'),
            "local_nbre_salle_bain"     => request('nbre_salle_bain'),
            "local_nbre_cuisine"        => request('nbre_cuisine'),
            "local_nbre_piscine"        => request('nbre_piscine'),
            "local_tom"                 => request('tom'),
            "local_tva"                 => request('tva'),
            "local_tlv"                 => request('tlv'),
            "local_timbre_principal"    => request('timbre_principal'),
            "local_timbre"              => request('timbre'),
            "local_eau_forfait"         => request('eau_forfait'),
            "local_photos"              => json_encode($files)
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

            $files = json_decode($request->kyc);
     
            for($i=0; $i<sizeof($files); $i++){
                if($files[$i]!='' && $files[$i]!=$request->namePhoto){
                    array_push($allFileName, $files[$i]); 
                } 
            }
    
            Local::where('local_id', request('identifiant'))
              ->update([
                "local_photos" => json_encode($allFileName)
            ]);

            // delete file

            File::delete(config('constants.PATH_BIEN').$request->namePhoto);
                

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
        $local =  Local::where('local_id',$id)->first();

        // Delete all files
        Helper::deleteFiles(config('constants.PATH_BIEN'), $local["local_photos"]);

        $local->delete();

        if($local){
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
