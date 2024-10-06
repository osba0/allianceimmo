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


        $locals = DB::table('locals')->where("bien_id", request('bien_id')); 

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

        $files = Helper::getFiles($request, config('constants.PATH_BIEN'), request('type_local'), 'local', $additionalFile);

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
            "local_photos"              => json_encode($files),
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
