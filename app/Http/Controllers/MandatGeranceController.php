<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Proprietaires;
use App\Models\Agence;
use App\Models\Personnels;
use App\Models\Biens;
use App\Models\MandatGerance;
use App\Models\Representant;

use App\Helpers\Helper;
use App\Http\Resources\MandatGeranceResource;

use DB;
use File;

class MandatGeranceController extends Controller
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

        // Get Agence
        $agence = Agence::get();

        // Get Personnel
        $personnels = Personnels::get();

        $data = ['proprietaires' => $listProprio, 'agence' => $agence, 'personnels' => $personnels];

        return view('mandat_gerance/index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $mandat =  MandatGerance::where('mandat_id', request('id_mandat'))->first(); 

        if($mandat){

            $base64_pdf = trim(request('file_genered'), "data:application/pdf;base64,");
            $base64_decode = base64_decode($base64_pdf);
            $pathFile = config('constants.PATH_MANDAT').request('name_file');
            $pdf = fopen($pathFile, 'w');
            fwrite($pdf, $base64_decode);
            fclose($pdf);

            $up = $mandat->update([
                "mandat_fichiers"  => $pathFile
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

            $q = new MandatGerance;

            $mandat_id = Helper::IDGenerator(new MandatGerance, 'mandat_id',config('constants.ID_LENGTH'), config('constants.PREFIX_MANDAT'));
      
            $q->mandat_id=$mandat_id;
            $q->proprio=request('proprio');
            $q->bien = request('bien');
            $q->agence=request('agence');
            $q->pers=request('pers');
            $q->mandat_duree=request('duree');
            $q->mandat_user=$user->username;
            $q->mandat_date_debut=request('date_debut');
            $q->mandat_date_fin=request('date_fin');
            $q->mandat_preavis_mandataire=request('preavis_mandataire');
            $q->mandat_preavis_proprio=request('preavis_proprio');
            $q->mandat_honoraire_gestion=request('honoraire_gestion');
            $q->mandat_position=request('position');
            

            $q->save();

        }catch(\Exceptions $e){
              return response([
                "code" => 1,
                "message" => $e->getMessage()
            ]);
        }

        return response([
            "code" => 0,
            "id_mandat" => $mandat_id,
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

        $mandats = MandatGerance::leftJoin('proprietaires', 'mandat_gerances.proprio', '=', 'proprietaires.proprio_id')
        ->leftJoin('agences', 'mandat_gerances.agence', '=', 'agences.agence_id')->leftJoin('biens', 'mandat_gerances.bien', '=', 'biens.bien_id')
        ->select('mandat_gerances.*','agences.agence_nom', 'proprietaires.proprio_nom', 'proprietaires.proprio_prenom', 'biens.bien_nom', 'biens.bien_adresse', 'biens.bien_numero')->groupBy('mandat_gerances.mandat_id');
        if(isset($paginate)){
           $mandats = $mandats->orderby("created_at", "desc")->paginate($paginate);
        }else{
            $mandats = $mandats->get();
        }

        //var_dump($mandats);die();

      
        return MandatGeranceResource::collection($mandats);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function getBienByProprio($id){
        $id_bien_exist = DB::table('mandat_gerances')->select('mandat_gerances.*')->where('proprio', '=', $id)->get()->toArray();
        $data = [];

        $listBiens = Biens::where("bien_proprio", $id);

        foreach ($id_bien_exist as $id_bien) {
            $data[] = $id_bien->bien;
        }

        if(sizeof($data) > 0){
            $listBiens = $listBiens->whereNotIn('bien_id', $data);
        }
       
        $listBiens = $listBiens->get();
        if($listBiens){
            return response([
                "code" => 0,
                "data" => $listBiens
            ]); 
        }
        
    }

    public function getRepresentantByProprio($id){
        $representants = Representant::where("repr_id_proprio", $id)->get();
        if($representants){
            return response([
                "code" => 0,
                "data" => $representants
            ]); 
        }
        
    }

    
}
