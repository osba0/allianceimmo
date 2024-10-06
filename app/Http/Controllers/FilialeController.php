<?php

namespace App\Http\Controllers;

use App\Models\Filiale;

use App\Helpers\Helper;

use App\Http\Resources\FilialeResource;

use Illuminate\Http\Request;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;



use DB;
use File;

class FilialeController extends Controller
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

            $q = new Filiale;

            $filiale_id = Helper::IDGenerator(new Filiale, 'filiale_id',config('constants.ID_LENGTH'), config('constants.PREFIX_FILIALE'));

            $q->filiale_id=$filiale_id;
            $q->agence_id=request('agence_id');
            $q->filiale_name=request('nom');
            $q->filiale_email=request('email');
            $q->filiale_ind1=request('ind1');
            $q->filiale_tel1=request('tel1');
            $q->filiale_adresse=request('adresse');
            $q->filiale_pays=request('pays');
            $q->filiale_ville=request('ville');
            $q->filiale_user=$user->username;

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


        $filiales = DB::table('filiales')->where("agence_id", request('agence_id'));

        if(isset($paginate)){
            $filiales = $filiales->orderby("created_at", "desc")->paginate($paginate);
        }else{
            $filiales = $filiales->get();
        }


        return FilialeResource::collection($filiales);
    }

     /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id, Request $request)
    {

        $local =  Filiale::where('repr_id', $id)->first();

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
