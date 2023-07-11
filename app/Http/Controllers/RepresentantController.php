<?php

namespace App\Http\Controllers;

use App\Models\Representant;

use App\Helpers\Helper;

use App\Http\Resources\RepresentantResource;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

use DB;
use File;

use Illuminate\Http\Request;

class RepresentantController extends Controller
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

            $q = new Representant;

            $repr_id = Helper::IDGenerator(new Representant, 'repr_id',config('constants.ID_LENGTH'), config('constants.PREFIX_REPRESENTANT'));
      
            $q->repr_id=$repr_id;
            $q->repr_id_proprio=request('proprio_id');
            $q->repr_civilite=request('civilite');
            $q->repr_nom=request('nom');
            $q->repr_prenom=request('prenom');
            $q->repr_indicatif_1=request('indicatif');
            $q->repr_tel_1=request('telephone');
            $q->repr_email=request('email');
            $q->repr_type_piece=request('type_piece');
            $q->repr_numero_piece=request('num_piece');
            $q->repr_user=$user->username;

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


        $rep = DB::table('representants')->where("repr_id_proprio", request('proprio_id')); 

        if(isset($paginate)){
            $rep = $rep->orderby("created_at", "desc")->paginate($paginate);
        }else{
            $rep = $rep->get();
        }

      
        return RepresentantResource::collection($rep);
    }

     /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id, Request $request)
    {

        $local =  Representant::where('repr_id', $id)->first(); 
         
        $up = $local->update([
            "repr_civilite"       => request('civilite'),
            "repr_nom"            => request('nom'),
            "repr_prenom"         => request('prenom'),
            "repr_indicatif_1"    => request('indicatif'),
            "repr_tel_1"          => request('telephone'),
            "repr_email"          => request('email'),
            "repr_type_piece"     => request('type_piece'),
            "repr_numero_piece"   => request('num_piece')
           
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
}
