<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Proprietaires;
use App\Models\Biens;
use App\Models\Local;

use App\Helpers\Helper;

use App\Http\Resources\BiensResource;

use DB;
use File;

class BiensController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Get Proprietaires
        $listProprio = Proprietaires::get();

        $data = ['proprietaires' => $listProprio];

        return view('biens/index', $data);
    }

    public function cartographie()
    {
        return view('biens/cartographie');
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

            $q = new Biens;

            $files = Helper::getFiles($request->TotalFiles, $request, config('constants.PATH_BIEN'), request('proprio'),  config('constants.PREFIX_BIEN'));

            $bien_id = Helper::IDGenerator(new Biens, 'bien_id',config('constants.ID_LENGTH'), config('constants.PREFIX_BIEN'));
      
            $q->bien_id=$bien_id;
            $q->bien_proprio=request('proprio');
            $q->bien_nom = request('nom');
            $q->bien_adresse=request('adresse');
            $q->bien_etage=request('etage');
            $q->bien_numero=request('numero');
            $q->user=$user->username;
            $q->bien_ville=request('ville');
            $q->bien_pays=request('pays');
            $q->bien_description=request('description');
            $q->bien_superficie=request('superficie');
            $q->latitude = request('latitude');
            $q->longitude = request('longitude');
            $q->bien_annee_construction=request('annee_construction');
            $q->bien_photos=json_encode($files);

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

        $biens = DB::table('biens')->leftJoin('proprietaires', 'biens.bien_proprio', '=', 'proprietaires.proprio_id')->leftJoin('locals', 'biens.bien_id', '=', 'locals.bien_id')->select('biens.*', DB::raw('COUNT(locals.local_id) as totalLocal'),'proprietaires.proprio_nom','proprietaires.proprio_prenom','proprietaires.created_at as crea_prop','proprietaires.proprio_tel_1', 'proprietaires.proprio_indicatif_1', 'proprietaires.updated_at as up_prop');

        if(request('proprioID')){
            $biens->where('biens.bien_proprio', request('proprioID'));
        }

        if(isset($paginate)){
            $biens = $biens->groupBy("biens.bien_id")->orderby("created_at", "desc")->paginate($paginate);
        }else{
            $biens = $biens->get();
        }

      
        return BiensResource::collection($biens);
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

        $files = Helper::getFiles($request->TotalFiles, $request, config('constants.PATH_BIEN'), request('proprio'),  config('constants.PREFIX_BIEN'), $additionalFile);

        $proprio =  Biens::where('bien_id', $id)->first(); 
         
        $up = $proprio->update([
            "bien_adresse"           => request('adresse'),
            "bien_etage"             => request('etage'),
            "bien_nom"               => request('nom'),
            "bien_numero"            => request('numero'),
            "bien_ville"             => request('ville'),
            "bien_pays"              => request('pays'),
            "bien_description"       => request('description'),
            "bien_superficie"        => request('superficie'),
            "bien_annee_construction"=> request('annee_construction'),
            "latitude"               => request('latitude'),
            "longitude"              => request('longitude'),
            "bien_photos"            => json_encode($files)
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
    
            Biens::where('bien_id', request('identifiant'))
              ->update([
                "bien_photos" => json_encode($allFileName)
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
        $bien =  Biens::where('bien_id',$id)->first();

        // Delete all files
        Helper::deleteFiles(config('constants.PATH_BIEN'), $bien["bien_photos"]);

        // Supprimer les locaux
        $locaux = Local::where('bien_id',$id)->first();

        if($locaux) $locaux->delete();


        $rem = Biens::where('bien_id',$id)->first();

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

   public function allWithLocation()
    {
        $biens = DB::table('biens')
            ->leftJoin('locals', 'biens.bien_id', '=', 'locals.bien_id')
            ->leftJoin('proprietaires', 'biens.bien_proprio', '=', 'proprietaires.proprio_id')
            ->whereNotNull('biens.latitude')
            ->whereNotNull('biens.longitude')
            ->select(
                'biens.*',
                'proprio_nom',
                'proprio_prenom',
                DB::raw('MAX(CASE WHEN locals.local_disponible = 1 THEN 1 ELSE 0 END) as has_local_disponible')
            )
            ->groupBy('biens.bien_id')
            ->get();

        return response()->json($biens);
    }
}
