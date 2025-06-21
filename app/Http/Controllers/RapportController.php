<?php

namespace App\Http\Controllers;

use App\Models\Proprietaires;
use App\Models\Agence;
use App\Models\Personnels;
use App\Models\Biens;
use App\Models\MandatGerance;
use App\Models\Locataires;
use App\Models\Local;
use App\Models\Bail;
use App\Models\PaiementsLoyer;
use App\Models\Operations;
use App\Models\ChargesFrais;
use App\Models\VersementLocataire;
use App\Models\MailEnAttente;

use App\Helpers\Helper;
use App\Http\Resources\OperationsResource;
use App\Http\Resources\PaiementLoyerResource;
use App\Http\Resources\ChargesResource;
use App\Http\Resources\RapportLocataireResource;
use App\Http\Resources\RapportAgenceResource;
use App\Http\Resources\RapportProprietaireResource;

use App\Services\Core\PdfService;

use Illuminate\Support\Facades\Auth;

use App\Http\Resources\LocalsResource;

use Carbon\Carbon;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use File;


class RapportController extends Controller
{

    /**
     * @var PdfService
    */
    protected $pdfService;

    public function __construct(PdfService $pdfService)
    {
        $this->middleware('auth'); //If user is not logged in then he can't access this page
        $this->pdfService = $pdfService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Get 
        // Get Proprietaires
        $listProprio = Proprietaires::get();

        // Get locataire
        $listLocataire = Locataires::get();

        // Get Personnels
        $personnels = Personnels::get();

        // Get Agence
        $agence = Agence::get();


        $data = ['proprietaires' => $listProprio, 'listLocataire' => $listLocataire, 'personnels' => $personnels, 'agence' => $agence];

        return view('rapports/index', $data);
    }

    public function rapportLoyers(Request $request)
    {
        $rapports = DB::table('paiements_loyers')
            ->join('bails', 'paiements_loyers.paiement_bail_id', '=', 'bails.bail_id')
            ->join('locataires', 'bails.bail_locataire', '=', 'locataires.locat_id')
            ->join('locals', 'bails.bail_local', 'like', DB::raw("CONCAT('%', locals.local_id, '%')"))
            ->join('biens', 'locals.bien_id', '=', 'biens.bien_id')
            ->leftJoin('proprietaires', 'biens.bien_proprietaire', '=', 'proprietaires.prop_id')
            ->select(
                'paiements_loyers.*',
                'locataires.locat_nom',
                'locataires.locat_prenom',
                'proprietaires.prop_nom',
                'biens.bien_adresse',
                'locals.local_type_local'
            )
            ->when($request->filled('locataire'), function ($query) use ($request) {
                $query->where('locataires.locat_id', $request->locataire);
            })
            ->when($request->filled('proprietaire'), function ($query) use ($request) {
                $query->where('proprietaires.prop_id', $request->proprietaire);
            })
            ->when($request->filled('mois'), function ($query) use ($request) {
                $query->whereMonth('paiements_loyers.created_at', $request->mois);
            })
            ->when($request->filled('annee'), function ($query) use ($request) {
                $query->whereYear('paiements_loyers.created_at', $request->annee);
            })
            ->orderBy('paiements_loyers.created_at', 'desc')
            ->get();

        return response()->json([
            'code' => 0,
            'data' => $rapports
        ]);
    }

    public function rapportLocataires(Request $request)
    {
        $paginate = $request->input('paginate');

        $baseQuery = DB::table('paiements_loyers')
            ->join('bails', 'paiements_loyers.paiement_bail_id', '=', 'bails.bail_id')
            ->join('biens', 'bails.bail_bien', '=', 'biens.bien_id')
            ->join('locataires', 'bails.bail_locataire', '=', 'locataires.locat_id')
            ->select(
                'paiements_loyers.paiement_id',
                'paiements_loyers.paiement_recu',
                'paiements_loyers.paiement_mois_location', //paiement_mois_location
                'paiements_loyers.paiement_montant',
                'paiements_loyers.paiement_etat',
                'bails.bail_montant_ht',
                'biens.bien_adresse',
                'locataires.locat_nom',
                'locataires.locat_prenom',
                'locataires.locat_id'
            )
            ->orderBy('paiements_loyers.paiement_mois_location', 'desc'); //paiement_mois_location

        // Filtres
        if ($request->filled('locataireID')) {
            $baseQuery->where('locataires.locat_id', $request->locataireID);
        }

        if ($request->filled('debut') && $request->filled('fin')) {

            $debut = Carbon::parse($request->debut)->startOfDay();
            $fin = Carbon::parse($request->fin)->endOfDay();

            $baseQuery->whereBetween('paiements_loyers.updated_at', [$debut, $fin]);

           // $baseQuery->whereBetween('paiements_loyers.updated_at', [$request->debut, $request->fin]); //paiement_mois_location
            // dd($request->debut, $request->fin, $baseQuery->toSql());
        }

        // Clonage pour total global avant pagination
        $totalQuery = clone $baseQuery;
        $paiementsBruts = $totalQuery->get();
        $totalGeneral = $paiementsBruts->reduce(function ($carry, $item) {
            $recu = json_decode($item->paiement_recu, true) ?? [];
            return $carry + collect($recu)->sum('paiementMontant');
        }, 0);

       /*
        $montantImpayes = (clone $totalQuery)
        ->where('paiement_etat', 0)
        ->sum('paiement_montant'); */

        // 1. Récupération de tous les paiements concernés
        $paiements = (clone $totalQuery)->get();

        // 2. Calcul du montant impayé réel (inclut les loyers totalement et partiellement impayés)
        $montantImpayesTotal = $paiements->reduce(function ($carry, $paiement) {
            $recu = json_decode($paiement->paiement_recu, true) ?? [];

            // Somme des montants validés uniquement
            $totalRecu = collect($recu)
                ->filter(fn($p) => $p['validate'] ?? false)
                ->sum('paiementMontant');

            // Le manque à gagner réel pour ce paiement
            $manque = max($paiement->paiement_montant - $totalRecu, 0);

            return $carry + $manque;
        }, 0);

        $totalLocataires = $paiementsBruts->pluck('locataires.locat_id')->unique()->count();
        $totalLignes = $paiementsBruts->count();

        // Pagination ou non
        if ($paginate) {
            $results = $baseQuery->paginate($paginate);
            $data = RapportLocataireResource::collection($results);
        } else {
            $results = $baseQuery->get();
            $data = RapportLocataireResource::collection($results);
        }

        return response()->json([
            'code' => 0,
            'data' => $data,
            'meta' => [
                'pagination' => $results, // 👈 très important
                'total_general' => $totalGeneral,
                'total_locataires' => $totalLocataires,
                'total_lignes' => $totalLignes,
                'total_impayes' => $montantImpayesTotal
            ]
        ]);
    }

    public function rapportLocataires2(Request $request)
    {
        $paginate = request('paginate');
         $query = DB::table('paiements_loyers')
        ->join('bails', 'paiements_loyers.paiement_bail_id', '=', 'bails.bail_id')
        ->join('biens', 'bails.bail_bien', '=', 'biens.bien_id')
        ->join('locataires', 'bails.bail_locataire', '=', 'locataires.locat_id')
        ->select(
            'paiements_loyers.paiement_id',
            'paiements_loyers.paiement_recu',
            'paiements_loyers.paiement_mois_location',
            'paiements_loyers.paiement_montant',
            'paiements_loyers.paiement_etat',
            'biens.bien_adresse',
            'locataires.locat_nom',
            'locataires.locat_prenom'
        )
        //->where('paiements_loyers.paiement_etat', 3) // uniquement les paiements validés
        ->orderBy('paiements_loyers.updated_at', 'desc');

        if ($request->filled('locataireID')) {
            $query->where('locataires.locat_id', $request->locataireID);
        }

        if ($request->filled('debut') && $request->filled('fin')) {
            $query->whereBetween('paiements_loyers.paiement_date', [$request->debut, $request->fin]);
        }

         if(isset($paginate)){
            $resultats = $query->paginate($paginate);
        }else{
            $resultats = $query->get();
        }

        // total général (même si page 1 seulement)
       /* $totalGlobal = $query->sum(DB::raw('JSON_EXTRACT(paiements_loyers.paiement_recu, "$[*].paiementMontant")'));

        var_dump($totalGlobal); die();*/

        return RapportLocataireResource::collection($resultats);
    }

    public function rapportProprietaires(Request $request)
    {
        /*$query = DB::table('paiements_loyers')
            ->join('bails', 'paiements_loyers.paiement_bail_id', '=', 'bails.bail_id')
            ->join('locals', DB::raw("JSON_UNQUOTE(JSON_EXTRACT(bails.bail_local, '$[0]'))"), '=', 'locals.local_id')
            ->join('biens', 'locals.bien_id', '=', 'biens.bien_id')
            ->join('proprietaires', 'biens.bien_proprio', '=', 'proprietaires.proprio_id')
            ->join('locataires', 'bails.bail_locataire', '=', 'locataires.locat_id')
            ->select(
                'proprietaires.proprio_nom',
                'proprietaires.proprio_prenom',
                'biens.bien_adresse',
                'locataires.locat_nom',
                'locataires.locat_prenom',
                DB::raw('SUM(paiements_loyers.paiement_montant) as montant_total')
            )
            ->groupBy(
                'proprietaires.proprio_nom',
                'proprietaires.proprio_prenom',
                'biens.bien_adresse',
                'locataires.locat_nom',
                'locataires.locat_prenom'
            )->where('paiements_loyers.paiement_etat', 3);*/

         // Requête pour les CREDITS (encaissements)
            $paginate = $request->input('paginate');
        $req_credits = DB::table('operations')
            ->join('bails', 'operations.oper_id_bail', '=', 'bails.bail_id')
            ->join('locals', DB::raw("JSON_UNQUOTE(JSON_EXTRACT(bails.bail_local, '$[0]'))"), '=', 'locals.local_id')
            ->join('biens', 'locals.bien_id', '=', 'biens.bien_id')
            ->join('proprietaires', 'biens.bien_proprio', '=', 'proprietaires.proprio_id')
            ->join('mandat_gerances', 'mandat_gerances.proprio', '=', 'proprietaires.proprio_id')
            ->join('locataires', 'bails.bail_locataire', '=', 'locataires.locat_id')
            ->select(
                'proprietaires.proprio_nom',
                'proprietaires.proprio_prenom',
                'biens.bien_adresse',
                'locals.local_type_local',
                'locals.local_nature_local',
                'locataires.locat_nom',
                'locataires.locat_prenom',
                'bails.bail_montant_ht',
                'mandat_gerances.mandat_honoraire_gestion',
                DB::raw('SUM(operations.oper_montant) as montant_total')
            )
            ->where('operations.oper_sens', 'CREDIT')
            ->groupBy(
                'proprietaires.proprio_nom',
                'proprietaires.proprio_prenom',
                'biens.bien_adresse',
                'locals.local_type_local',
                'locals.local_nature_local',
                'locataires.locat_nom',
                'locataires.locat_prenom',
                'bails.bail_montant_ht',
                'mandat_gerances.mandat_honoraire_gestion'
            );

        // Requête pour les DEBITS (frais ou retenues)
        $req_debits = DB::table('operations')
            //->join('bails', DB::raw("JSON_UNQUOTE(JSON_EXTRACT(operations.oper_id_bail, '$[0]'))"), '=', 'bails.bail_id')
            //->join('locals', DB::raw("JSON_UNQUOTE(JSON_EXTRACT(bails.bail_local, '$[0]'))"), '=', 'locals.local_id')
            ->join('charges_frais', 'operations.oper_id_charge', '=', 'charges_frais.charge_id')
            ->join('locals', 'charges_frais.charge_id_local', '=', 'locals.local_id')
            ->join('biens', 'locals.bien_id', '=', 'biens.bien_id')
            ->join('proprietaires', 'biens.bien_proprio', '=', 'proprietaires.proprio_id')
           // ->join('mandat_gerances', 'mandat_gerances.proprio', '=', 'proprietaires.proprio_id')
           //  ->join('locataires', 'bails.bail_locataire', '=', 'locataires.locat_id')
            ->select(
                'charges_frais.charge_note',
                'charges_frais.charge_montant',
                'proprietaires.proprio_nom',
                'proprietaires.proprio_prenom',
                'biens.bien_adresse',
                'locals.local_type_local',
                'locals.local_nature_local',
           //     'locataires.locat_nom',
           //     'locataires.locat_prenom',
                DB::raw('SUM(operations.oper_montant) as montant_debit')
            )
            ->where('operations.oper_sens', 'DEBIT')
            ->groupBy(
                'charges_frais.charge_note',
                'charges_frais.charge_montant',
                'proprietaires.proprio_nom',
                'proprietaires.proprio_prenom',
                'biens.bien_adresse',
                'locals.local_type_local',
                'locals.local_nature_local',
             //   'locataires.locat_nom',
             //   'locataires.locat_prenom'
            );

        if($request->filled('proprioID')){
            $req_credits->where('proprietaires.proprio_id', $request->proprioID);
            $req_debits->where('proprietaires.proprio_id', $request->proprioID);
        }

        if ($request->filled('debut') && $request->filled('fin')) {
            $req_credits->whereBetween('operations.created_at', [$request->debut.' 00:00:00', $request->fin.' 23:59:59']);
            $req_debits->whereBetween('operations.created_at', [$request->debut.' 00:00:00', $request->fin.' 23:59:59']);
        }

        $req_credits = $req_credits->orderBy('proprietaires.proprio_nom');




         if ($paginate) {
            $results = $req_credits->paginate($paginate);
            $data = RapportProprietaireResource::collection($results);
        } else {
            $results = $req_credits->get();
            $data = RapportProprietaireResource::collection($results);
        }

        $debits = $req_debits->orderBy('proprietaires.proprio_nom')->get();

        return response()->json([
            'code' => 0,
            'data' => $data,
            'meta' => [
                'pagination' => $results, // 👈 très important,
            ]
        ]);

        //return response()->json(['code' => 0, 'data' => $credits, 'debits' => $debits]);


    }

    public function rapportPDFProprietaires(Request $request)
    {

        $query = DB::table('operations')
        ->join('bails', 'operations.oper_id_bail', '=', 'bails.bail_id')
        ->join('locals', DB::raw("JSON_UNQUOTE(JSON_EXTRACT(bails.bail_local, '$[0]'))"), '=', 'locals.local_id')
        ->join('biens', 'locals.bien_id', '=', 'biens.bien_id')
        ->join('proprietaires', 'biens.bien_proprio', '=', 'proprietaires.proprio_id')
        ->join('mandat_gerances', 'mandat_gerances.proprio', '=', 'proprietaires.proprio_id')
        ->join('locataires', 'bails.bail_locataire', '=', 'locataires.locat_id')
        ->select(
            'proprietaires.proprio_nom',
            'proprietaires.proprio_prenom',
            'biens.bien_adresse',
            'locals.local_type_local',
            'locals.local_nature_local',
            'locataires.locat_nom',
            'locataires.locat_prenom',
            'bails.bail_montant_ht',
            'mandat_gerances.mandat_honoraire_gestion',
            DB::raw('SUM(operations.oper_montant) as montant_total')
        )
        ->groupBy(
            'proprietaires.proprio_nom',
            'proprietaires.proprio_prenom',
            'biens.bien_adresse',
            'locals.local_type_local',
            'locals.local_nature_local',
            'locataires.locat_nom',
            'locataires.locat_prenom',
            'bails.bail_montant_ht',
            'mandat_gerances.mandat_honoraire_gestion'
        )
        ->where('operations.oper_sens', 'CREDIT');

         // Requête pour les DEBITS (frais ou retenues)
        $req_debits = DB::table('operations')
            ->join('charges_frais', 'operations.oper_id_charge', '=', 'charges_frais.charge_id')
            ->join('locals', 'charges_frais.charge_id_local', '=', 'locals.local_id')
            ->join('biens', 'locals.bien_id', '=', 'biens.bien_id')
            ->join('proprietaires', 'biens.bien_proprio', '=', 'proprietaires.proprio_id')
            ->select(
                'charges_frais.charge_note',
                'charges_frais.charge_montant',
                'charges_frais.created_at',
                'proprietaires.proprio_nom',
                'proprietaires.proprio_prenom',
                'biens.bien_adresse',
                'locals.local_type_local',
                'locals.local_nature_local',
           //     'locataires.locat_nom',
           //     'locataires.locat_prenom',
                DB::raw('SUM(operations.oper_montant) as montant_debit')
            )
            ->where('operations.oper_sens', 'DEBIT')
            ->groupBy(
                'charges_frais.charge_note',
                'charges_frais.charge_montant',
                'charges_frais.created_at',
                'proprietaires.proprio_nom',
                'proprietaires.proprio_prenom',
                'biens.bien_adresse',
                'locals.local_type_local',
                'locals.local_nature_local',
             //   'locataires.locat_nom',
             //   'locataires.locat_prenom'
            );

        $soldeAnterieur = DB::table('operations')
            ->join('bails', 'operations.oper_id_bail', '=', 'bails.bail_id')
            ->join('locals', DB::raw("JSON_UNQUOTE(JSON_EXTRACT(bails.bail_local, '$[0]'))"), '=', 'locals.local_id')
            ->join('biens', 'locals.bien_id', '=', 'biens.bien_id')
            ->where('biens.bien_proprio', $request->proprioID)
            ->whereDate('operations.created_at', '<=', $request->debut)
            ->select(DB::raw("
                SUM(CASE WHEN oper_sens = 'CREDIT' THEN oper_montant ELSE 0 END)
                -
                SUM(CASE WHEN oper_sens = 'DEBIT' THEN oper_montant ELSE 0 END)
                as solde_anterieur
            "))
            ->value('solde_anterieur');



        if($request->filled('proprioID')){
            $query->where('proprietaires.proprio_id', $request->proprioID);
            $req_debits->where('proprietaires.proprio_id', $request->proprioID);
        }

        if ($request->filled('debut') && $request->filled('fin')) {
            $query->whereBetween('operations.created_at', [$request->debut, $request->fin]);
            $req_debits->whereBetween('operations.created_at', [$request->debut.' 00:00:00', $request->fin.' 23:59:59']);
        }

        $rapports = $query->orderBy('proprietaires.proprio_nom')->get();
        $debits = $req_debits->orderBy('proprietaires.proprio_nom')->get();

        $currentYear = date('Y');

        // Définir le chemin du dossier de l'année en cours
        $path = strtolower("encaissement_loyer/{$currentYear}/{$request->proprioID}");
        $directoryPath = public_path($path);

        // Créer le dossier s'il n'existe pas
        if (!File::exists($directoryPath)) {
            File::makeDirectory($directoryPath, 0755, true);
        }

        // Générer le nom avec la periode
        $fileName = strtolower("{$request->proprioID}_" . $request->debut . "_{$request->fin}.pdf");


        // Chemin complet du fichier
        $filePath = "{$directoryPath}/{$fileName}";

        $data = [
            'rapports' => $rapports,
            'debits' => $debits,
            'meta' => [
                'soldeAnterieur' => $soldeAnterieur,
                'proprietaire' => $request->proprietaire,
                'proprietaire_indicatif'=> $request->proprietaire_indicatif,
                'proprietaire_telephone'=> $request->proprietaire_telephone,
                'proprietaire_adresse'=> $request->proprietaire_adresse,
                'proprietaire_ville'=> $request->proprietaire_ville,
                'proprietaire_pays'=> $request->proprietaire_pays,
                'pathFile' => $path,
                'fichier' => "/{$path}/{$fileName}",
                'debut' => $request->debut,
                'fin' => $request->fin
            ]
        ];



        // Générer le PDF et le sauvegarder
        $pdf = $this->pdfService->generationPDF(
            config('common.pdf.encaissement_loyer.template'),
            $data,
            $filePath
        );

        $publicPath = public_path($path);

        if($pdf){
            return response()->json(['code' => 0, 'file_path' => "/{$path}/{$fileName}", "public_path" => "{$path}/{$fileName}"]);
        }else{
            return response()->json(['code' => 1, 'message' => 'Erreur de génération du rapport']);
        }


    }

    public function rapportAgenceOld()
    {
        $moisActuel = Carbon::now()->format('Y-m');

        $revenusParMois = DB::table('paiements_loyers')
            ->select(
                DB::raw("DATE_FORMAT(paiement_date, '%Y-%m') as mois"),
                DB::raw('SUM(paiement_montant) as total')
            )
            ->groupBy('mois')
            ->orderByDesc('mois')
            ->limit(12)
            ->get();

        $impayes = DB::table('bails')
            ->leftJoin('paiements_loyers', 'bails.bail_id', '=', 'paiements_loyers.paiement_bail_id')
            ->select(
                'bails.bail_id',
                DB::raw('bails.bail_montant_ht - IFNULL(SUM(paiements_loyers.paiement_montant), 0) as reste_a_payer')
            )
            ->groupBy('bails.bail_id', 'bails.bail_montant_ht')
            ->having('reste_a_payer', '>', 0)
            ->get();

        return response()->json([
            'code' => 0,
            'revenus_mensuels' => $revenusParMois,
            'impayes' => $impayes
        ]);
    }

    public function envoisRapportProprio(Request $request){

        $data['nom_prenom_proprio'] = $request->proprietaire;
        $data['debut'] = $request->debut;
        $data['fin'] = $request->fin;
        $data['agence_id'] = auth()->user()->agence_id;
        $data['path_rapport'] = $request->rapportGenerate;
        // Preparer le mail
        $sent = MailEnAttente::create([
            'email_destinataire' => $request->proprio_email,
            'email_cc' => "",
            'sujet' => 'DETAILS ENCAISSEMENTS LOYERS PERIODE : '.$data['debut'].' au '.$data['fin'],
            'action'=> 'RELEVE ENCAISSEMENTS LOYERS',
            'contenu_html' => '',
            'contenu_text' => '',
            'fichier_joint' => $request->rapportGenerate,
            'etat' => 'en_attente',
            'template' => 'rapport_encaissement',
            'data' => json_encode($data)
        ]);

        if($sent){
            return response()->json(['code' => 0, 'message' => "ok" ]);
        }else{
            return response()->json(['code' => 1, 'message' => 'Erreur']);
        }
    }

    public function rapportPDFLocataire(Request $request){
        $baseQuery = DB::table('paiements_loyers')
            ->join('bails', 'paiements_loyers.paiement_bail_id', '=', 'bails.bail_id')
            ->join('biens', 'bails.bail_bien', '=', 'biens.bien_id')
            ->join('locals', 'bails.bail_local', 'like', DB::raw("CONCAT('%', locals.local_id, '%')"))
            ->join('locataires', 'bails.bail_locataire', '=', 'locataires.locat_id')
            ->select(
                'paiements_loyers.paiement_id',
                'paiements_loyers.paiement_recu',
                'paiements_loyers.paiement_mois_location',
                'paiements_loyers.paiement_montant',
                'paiements_loyers.paiement_etat',
                'bails.bail_montant_ht',
                'biens.bien_adresse',
                'locataires.locat_nom',
                'locataires.locat_prenom',
                'locataires.locat_id',
                'local_type_local',
                'local_nature_local'
            )
            ->orderBy('paiements_loyers.paiement_mois_location', 'desc');

        // Filtres
        if ($request->filled('locataireID')) {
            $baseQuery->where('locataires.locat_id', $request->locataireID);
        }

        if ($request->filled('debut') && $request->filled('fin')) {

            $debut = Carbon::parse($request->debut)->startOfDay();
            $fin = Carbon::parse($request->fin)->endOfDay();

            $baseQuery->whereBetween('paiements_loyers.updated_at', [$debut, $fin]);
            //$baseQuery->whereBetween('paiements_loyers.updated_at', [$request->debut, $request->fin]); //paiement_mois_location
        }

        // Clonage pour total global avant pagination
        $totalQuery = clone $baseQuery;
        $paiementsBruts = $totalQuery->get();
        $totalPaye = $paiementsBruts->reduce(function ($carry, $item) {
            $recu = json_decode($item->paiement_recu, true) ?? [];
            return $carry + collect($recu)->sum('paiementMontant');
        }, 0);

        /*$montantImpayes = $totalQuery
        ->where('paiement_etat', 0)
        ->sum('paiement_montant');*/

        // 1. Récupération de tous les paiements concernés
        $paiements = (clone $totalQuery)->get();

        // 2. Calcul du montant impayé réel (inclut les loyers totalement et partiellement impayés)
        $montantImpayesTotal = $paiements->reduce(function ($carry, $paiement) {
            $recu = json_decode($paiement->paiement_recu, true) ?? [];

            // Somme des montants validés uniquement
            $totalRecu = collect($recu)
                ->filter(fn($p) => $p['validate'] ?? false)
                ->sum('paiementMontant');

            // Le manque à gagner réel pour ce paiement
            $manque = max($paiement->paiement_montant - $totalRecu, 0);

            return $carry + $manque;
        }, 0);

        $totalLocataires = $paiementsBruts->pluck('locat_id')->unique()->count();
        $totalLignes = $paiementsBruts->count();

        $results = $baseQuery->get();
        $rapports = RapportLocataireResource::collection($results);

        $currentYear = date('Y');

        // Définir le chemin du dossier de l'année en cours
        $path = strtolower("rapport_locataire/{$currentYear}/{$request->locataireID}");
        $directoryPath = public_path($path);

        // Créer le dossier s'il n'existe pas
        if (!File::exists($directoryPath)) {
            File::makeDirectory($directoryPath, 0755, true);
        }

        // Générer le nom avec la periode
        $fileName = strtolower("{$request->locataireID}_" . $request->debut . "_{$request->fin}.pdf");


        // Chemin complet du fichier
        $filePath = "{$directoryPath}/{$fileName}";

        $data = [
            'rapports' => $rapports,
            'meta' => [
                'locataire' => $request->locataire,
                'locataire_indicatif'=> $request->locataire_indicatif,
                'locataire_telephone'=> $request->locataire_telephone,
                'locataire_adresse'=> $request->locataire_adresse,
                'locataire_ville'=> $request->locataire_ville,
                'locataire_pays'=> $request->locataire_pays,
                'pathFile' => $path,
                'fichier' => "/{$path}/{$fileName}",
                'debut' => $request->debut,
                'fin' => $request->fin,
                'total_paye' => $totalPaye,
                'total_impayes' => $montantImpayesTotal, //$montantImpayes,
            ]
        ];



        // Générer le PDF et le sauvegarder
        $pdf = $this->pdfService->generationPDF(
            config('common.pdf.rapport_locataire.template'),
            $data,
            $filePath
        );

        $publicPath = public_path($path);

        if($pdf){
            return response()->json(['code' => 0, 'file_path' => "/{$path}/{$fileName}", "public_path" => "{$path}/{$fileName}"]);
        }else{
            return response()->json(['code' => 1, 'message' => 'Erreur de génération du rapport']);
        }
    }

    public function envoisRapportLocataire(Request $request){
        $data['nom_prenom_locataire'] = $request->locataire;
        $data['debut'] = $request->debut;
        $data['fin'] = $request->fin;
        $data['agence_id'] = auth()->user()->agence_id;
        $data['path_rapport'] = $request->rapportGenerate;
        // Preparer le mail
        $sent = MailEnAttente::create([
            'email_destinataire' => $request->locataire_email,
            'email_cc' => "",
            'sujet' => 'DETAILS LOYERS PERIODE : '.$data['debut'].' au '.$data['fin'],
            'action'=> 'ETAT DES LOYERS',
            'contenu_html' => '',
            'contenu_text' => '',
            'fichier_joint' => $request->rapportGenerate,
            'etat' => 'en_attente',
            'template' => 'rapport_locataire',
            'data' => json_encode($data)
        ]);

        if($sent){
            return response()->json(['code' => 0, 'message' => "ok" ]);
        }else{
            return response()->json(['code' => 1, 'message' => 'Erreur']);
        }
    }

     public function rapportAgence(Request $request)
    {
        $paginate = $request->input('paginate');

        $baseQuery = DB::table('paiements_loyers')
            ->join('bails', 'paiements_loyers.paiement_bail_id', '=', 'bails.bail_id')
            ->join('biens', 'bails.bail_bien', '=', 'biens.bien_id')
            ->join('locataires', 'bails.bail_locataire', '=', 'locataires.locat_id')
            ->select(
                'paiements_loyers.paiement_id',
                'paiements_loyers.paiement_recu',
                'paiements_loyers.paiement_montant',
                'paiements_loyers.paiement_etat',
                'paiements_loyers.paiement_mois_location',
                'paiements_loyers.paiement_bail_id',
                'biens.bien_nom',
                'biens.bien_adresse',
                'bails.bail_montant_ht',
                'locataires.locat_nom',
                'locataires.locat_prenom',
                'locataires.locat_id'
            );

        if ($request->filled('debut') && $request->filled('fin')) {
            $debut = Carbon::parse($request->debut)->startOfDay();
            $fin = Carbon::parse($request->fin)->endOfDay();

            $baseQuery->whereBetween('paiements_loyers.updated_at', [$debut, $fin]);
            //$baseQuery->whereBetween('paiements_loyers.updated_at ', [$request->debut, $request->fin]); //paiement_mois_location
        }

        // Clonage pour total global avant pagination
        $totalQuery = clone $baseQuery;
        $paiementsBruts = $totalQuery->get();

        $totalPaye = $paiementsBruts->reduce(function ($carry, $item) {
            $recu = json_decode($item->paiement_recu, true) ?? [];
            return $carry + collect($recu)->sum('paiementMontant');
        }, 0);

        $totalAttendu = $paiementsBruts->sum('bail_montant_ht');
        $totalImpayes = max($totalAttendu - $totalPaye, 0);
        $tauxRecouvrement = $totalAttendu > 0 ? round(($totalPaye / $totalAttendu) * 100, 2) : 0;
        $totalBaux = $paiementsBruts->pluck('paiement_bail_id')->unique()->count();
        $totalLocataires = $paiementsBruts->pluck('locat_id')->unique()->count();

        if ($paginate) {
            $results = $baseQuery->paginate($paginate);
            $data = RapportAgenceResource::collection($results);
        } else {
            $results = $baseQuery->get();
            $data = RapportAgenceResource::collection($results);
        }

        return response()->json([
            'code' => 0,
            'data' => $data,
            'meta' => [
                'pagination' => $results, // 👈 très important,
                'total_paye' => $totalPaye,
                'total_attendu' => $totalAttendu,
                'total_impayes' => $totalImpayes,
                'taux_recouvrement' => $tauxRecouvrement,
                'total_baux' => $totalBaux,
                'total_locataires' => $totalLocataires,
            ]
        ]);
    }

}
