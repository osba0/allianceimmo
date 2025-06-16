<?php

namespace App\Http\Controllers;

use App\Models\Proprietaires;

use Illuminate\Http\Request;

use App\Models\VersementProprio;
use App\Models\Operations;
use App\Models\MailEnAttente;

use App\Http\Resources\VersementProprioResource;


use Carbon\Carbon;
use App\Helpers\Helper;

use Illuminate\Support\Facades\Auth;

use App\Services\NotificationService;

use DB;
use File;

class VersementsController extends Controller
{
    public function index()
    {

        return view('versements/index');
    }

     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function all()
    {
        $user = Auth::user();

        $paginate = request('paginate');

        $versements = VersementProprio::leftJoin('proprietaires', 'versements_proprietaires.versement_proprio_id', '=', 'proprietaires.proprio_id')
            ->leftJoin('biens', 'versements_proprietaires.versement_bien_id', '=', 'biens.bien_id')
            ->leftJoin('locals', 'biens.bien_id', '=', 'locals.local_id')
            ->select(
                'versements_proprietaires.*',
                'proprietaires.proprio_nom',
                'proprietaires.proprio_prenom',
                'biens.bien_nom',
                'biens.bien_adresse',
                'biens.bien_numero',
                'biens.bien_ville',
                'biens.bien_pays',
                'locals.local_type_local'
            );

        if(request('proprioID')){
            $versements->where('proprietaires.proprio_id', request('proprioID'));
        }
        if(isset($paginate)){
            $versements = $versements->orderby("versements_proprietaires.created_at", "desc")->paginate($paginate);
        }else{
            $versements = $versements->get();
        }


        return VersementProprioResource::collection($versements);
    }


    public function ajoutVersement(Request $request){

    $user = Auth::user();

     // Save versement
        try{

            $v = new VersementProprio;

            $versement_id = Helper::IDGenerator(new VersementProprio, 'versement_id',config('constants.ID_LENGTH'), config('constants.PREFIX_VERSEMENT_PROPRIO'));

            $files = Helper::getFiles($request->TotalFiles, $request, config('constants.PATH_VERSEMENT'), request('proprio'),  config('constants.PREFIX_VERSEMENT_PROPRIO'));

            $v->versement_id=$versement_id;
            $v->versement_type=request('type');
            $v->versement_moyen_paiement=request('moyen_paiement');
            $v->versement_description=request('note');
            $v->versement_date=request('date');
            $v->versement_montant=(double) str_replace(' ', '', request('montant'));
            $v->versement_proprio_id=request('proprio');
            $v->versement_bien_id=request('bien');
            $v->versement_user=$user->username;
            $v->versement_fichier=json_encode($files);


            $v->save();

            // Save dans Operation

            $operation = new Operations;

            $oper_id = Helper::IDGenerator(new Operations, 'oper_id',config('constants.ID_LENGTH'), config('constants.PREFIX_OPERATION'));

            $operation->oper_id=$oper_id;
            $operation->oper_sens=config('constants.CREDIT');
            $operation->oper_type=Operations::VERSEMENT_PROPRIO;
            $operation->oper_note=request('note');
            $operation->oper_statut='valide'; // A enlever pour le module caissier car par default c'est en attente
            $operation->oper_montant=request('montant');
            $operation->oper_id_versement_proprio=$versement_id;
            $operation->oper_user=$user->username;


            $operation->save();

            // Preparer le mail
            $data['proprio_nom'] = request('proprio_nom');
            $data['proprio_prenom'] = request('proprio_prenom');
            $data['montant'] = request('montant');
            $data['date'] = request('date');
            $data['type'] = request('type');
            $data['note'] = request('note');
            $data['created_by'] = $user->username;
            $data['bien_id'] = request('bien');
            $data['agence_id'] = auth()->user()->agence_id;
            $sent = MailEnAttente::create([
                'email_destinataire' => request('proprio_email'),
                'email_cc' => null,
                'sujet' => 'Nouveau versement enregistré',
                'action' => 'Ajout d’un versement',
                'contenu_html' => '',
                'contenu_text' => '',
                'fichier_joint' => '',
                'etat' => 'en_attente',
                'template' => 'nouveau_versement',
                'data' => json_encode($data),
            ]);


            // Lorsqu'un nouveau locataire est créé
            NotificationService::creerNotification(
                'creation',
                'Versement enregistré',
                "Un nouveau versement a été enregistré pour le propriétaire.",
                [
                    'proprio_id' => request('proprio'),
                    'proprio_nom' => request('proprio_nom'),
                    'proprio_prenom' => request('proprio_prenom'),
                ]
            );



        }catch(\Exceptions $e){
              return response([
                "code" => 1,
                "message" => $e->getMessage()
            ]);
        }

        if($v){
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
        $verse = VersementProprio::where('versement_id',$id)->first();

        // Delete all files
        Helper::deleteFiles(config('constants.PATH_VERSEMENT'), $verse["versement_fichier"]);

        // Supprimer les operation du versement associés
        $operation = Operations::where('oper_id_versement_proprio',$id)->first();

        if($operation) $operation->delete();


        $verse->delete();

        if($verse){
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
