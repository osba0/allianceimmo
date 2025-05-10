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

use App\Services\Core\PdfService;

use Illuminate\Support\Facades\Auth;

use App\Http\Resources\LocalsResource;

use App\Services\NotificationService;

use Carbon\Carbon;

use DB;
use File;

use Illuminate\Http\Request;

class OperationsController extends Controller
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
        // Get Proprietaires
        $listProprio = Proprietaires::get();
        
        // Get locataire
        $listLocataire = Locataires::get();

        // Get Personnels
        $personnels = Personnels::get();

        // Get Agence
        $agence = Agence::get();


        $data = ['proprietaires' => $listProprio, 'listLocataire' => $listLocataire, 'personnels' => $personnels, 'agence' => $agence];


        return view('operations/index', $data);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function list()
    {
        /*
        // Définir l'année actuelle
        $currentYear = date('Y');

        // Définir le chemin du dossier de l'année en cours
        $directoryPath = public_path("uploads/{$currentYear}");

        // Créer le dossier s'il n'existe pas
        if (!File::exists($directoryPath)) {
            File::makeDirectory($directoryPath, 0755, true);
        }

        // Générer le nom de fichier avec la date et l'heure actuelles
        $timestamp = date('Y-m-d_H-i-s');
        $fileName = "quittance_loyer_{$timestamp}.pdf";

        // Chemin complet du fichier
        $filePath = "{$directoryPath}/{$fileName}";

        // Supprimer le fichier s'il existe déjà
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        // Générer le PDF et le sauvegarder
        $pdf = $this->pdfService->quittanceLoyer(
            config('common.pdf.quittance_loyer.template'),
            ['operations' => 'lolo'],
            $filePath
        );

        if (!$pdf) {
            throw new PrintException("Pdf has not been generated");
        }*/
        // Get Proprietaires
        $listProprio = Proprietaires::get();
        
        // Get locataire
        $listLocataire = Locataires::get();

        // Get Personnels
        $personnels = Personnels::get();

        // Get Agence
        $agence = Agence::get();


        $data = ['proprietaires' => $listProprio, 'listLocataire' => $listLocataire, 'personnels' => $personnels, 'agence' => $agence];

        return view('operations/list', $data);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function charges()
    {
        // Get Proprietaires
        $listProprio = Proprietaires::get();
        
        // Get locataire
        $listLocataire = Locataires::get();

        // Get Personnels
        $personnels = Personnels::get();

        // Get Agence
        $agence = Agence::get();


        $data = ['proprietaires' => $listProprio, 'listLocataire' => $listLocataire, 'personnels' => $personnels, 'agence' => $agence];

        return view('operations/charge', $data);
    }

     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function listOperation()
    {
        $user = Auth::user();

        $paginate = request('paginate');  

        $oper = $oper = Operations::leftJoin('bails', 'operations.oper_id_bail', '=', 'bails.bail_id')->leftJoin('charges_frais', 'operations.oper_id_charge', '=', 'charges_frais.charge_id')->leftJoin('versements_proprietaires', 'operations.oper_id_versement_proprio', '=', 'versements_proprietaires.versement_id')
        ->select('operations.*','versements_proprietaires.versement_proprio_id', 'bails.bail_locataire', 'bails.bail_local','bails.bail_proprio', 'charges_frais.charge_id_proprio', 'charges_frais.charge_id_bien', 'charges_frais.charge_id_local')->groupBy('operations.oper_id');
        if(isset($paginate)){
            $oper = $oper->orderby("operations.created_at", "desc")->paginate($paginate);
        }else{
            $oper = $oper->get();
        }

      
        return OperationsResource::collection($oper);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function listingCharges()
    {
        $user = Auth::user();

        $paginate = request('paginate');  

        $charges = ChargesFrais::leftJoin('proprietaires', 'charges_frais.charge_id_proprio', '=', 'proprietaires.proprio_id')->leftJoin('biens', 'charges_frais.charge_id_bien', '=', 'biens.bien_id')->leftJoin('locals', 'charges_frais.charge_id_local', '=', 'locals.local_id')
        ->select('charges_frais.*', 'proprietaires.proprio_nom','proprietaires.proprio_prenom', 'biens.bien_nom', 'biens.bien_adresse', 'biens.bien_numero', 'locals.local_type_local')->groupBy('charges_frais.charge_id');
        if(request('proprioID')){
            $charges->where('proprietaires.proprio_id', request('proprioID'));
        }
        if(isset($paginate)){
            $charges = $charges->orderby("charges_frais.created_at", "desc")->paginate($paginate);
        }else{
            $charges = $charges->get();
        }

      
        return ChargesResource::collection($charges);
    }

    public function checkLoyerIsPayable($id, $mois)
    {
        $user = Auth::user();
        // les loyers generé manuellement sont automatiquement editable
        $paiementManual = PaiementsLoyer::select('paiement_etat')->where("paiement_mois_location", $mois)->
        where("paiement_user",'!=','SYSTEM')->where("paiement_bail_id", $id)->first();

        if($paiementManual){
            return response()->json([
                'code' => 0,
                'isPayable' => true
            ]);
        }
        // sinon on continue...

        $moisPreview = Carbon::createFromFormat('Y-m', $mois)->subMonth();

        $paiement = PaiementsLoyer::select('paiement_etat')->where("paiement_mois_location", $moisPreview->format('Y-m'))->where("paiement_bail_id", $id)->first(); //

        $isPayable = false;

        if($paiement){
            if($paiement->paiement_etat == 3){
                $isPayable = true;
            }else{
                $isPayable = false;
            }
        }else{
            $isPayable = true;
        }

        return response()->json([
            'code' => 0,
            'isPayable' => $isPayable
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function listingPaimentLoyer()
    {
        $user = Auth::user();

        $paginate = request('paginate');  

        $filtreLocataire = request('locataireFiltre');

        $paiement = PaiementsLoyer::leftJoin('bails', 'bails.bail_id', '=', 'paiements_loyers.paiement_bail_id')->leftJoin('proprietaires', 'bails.bail_proprio', '=', 'proprietaires.proprio_id')
        ->select('paiements_loyers.*', 'bails.bail_proprio', 'proprietaires.proprio_nom','proprietaires.proprio_prenom','bails.bail_locataire', 'bails.bail_local', 'bails.bail_montant_ht');
        if(!is_null($filtreLocataire)){
            $paiement = $paiement->where("bail_locataire",$filtreLocataire);
        }
        $paiement = $paiement->groupBy('paiements_loyers.paiement_id');
        if(isset($paginate)){
            $paiement = $paiement->orderby("paiements_loyers.paiement_mois_location", "desc")->paginate($paginate);
        }else{
            $paiement = $paiement->orderby("paiements_loyers.paiement_mois_location", "desc")->get();
        }

      
        return PaiementLoyerResource::collection($paiement);
    }

    public function getloyersActifByLocataire(){
        $localActif = Local::leftJoin('bails', DB::raw("JSON_CONTAINS(bails.bail_local, JSON_QUOTE(locals.local_id))"), '=', DB::raw('true'))->leftJoin('biens', 'biens.bien_id', '=', 'locals.bien_id')->select('locals.*', 'biens.*', 'bails.*')->where("bail_etat", true)->get();

         return response([
                "code" => 0,
                "data" => $localActif
            ]);

    }

    public function ajoutPaiement(Request $request){

        $user = Auth::user();

        $files = Helper::getFiles($request->TotalFiles, $request, config('constants.PATH_DOC_JUSTI'), strtolower(request('id_locataire')), 'justificatif');

        $paiement_list = json_decode($request->paiements);

        $calul_montant = 0;
        $montant_recu = 0;
        $mntPreleve = 0;
        $urlQuittance = "";

        $resultPaiementSolde = 'Solde non utilise';

        if($request->useSolde === 'true'){

            $soldeAvant = VersementLocataire::where('locataire_id', $request->id_locataire)
                            ->where('statut', 'actif')
                            ->sum('solde_disponible');

            $resultPaiementSolde = $this->payerLoyer($request);

            if($resultPaiementSolde['code'] == 0){
                $mntRestant = $resultPaiementSolde['mnt_reste_a_payer'];
                $mntPreleve = $soldeAvant - $resultPaiementSolde['solde_apres'];
            }

        }


        DB::beginTransaction();
        try {
            // get data
            $paymentdata = PaiementsLoyer::leftJoin('bails', 'bails.bail_id', '=', 'paiements_loyers.paiement_bail_id')->leftJoin('proprietaires', 'bails.bail_proprio', '=', 'proprietaires.proprio_id')->leftJoin('locataires', 'bails.bail_locataire', '=', 'locataires.locat_id')->leftJoin('biens', 'bails.bail_bien', '=', 'biens.bien_id')
            ->select('paiements_loyers.*', 'bails.bail_proprio', 'proprietaires.*','biens.*', 'locataires.*','bails.bail_locataire', 'bails.bail_local', 'bails.bail_montant_ht')->where('paiement_id', request('id_loyer'))->first();

            if($paymentdata) {
                $paymentArray = $paymentdata->toArray(); // Si c'est un modèle Eloquent

            } else {
                $paymentArray = []; // Renvoie un tableau vide si aucun résultat
            }




            $paiement_list = $this->generateRecu($request, $paymentArray, $paiement_list, $mntPreleve, json_encode($files));



            foreach($paiement_list as $paiement){
                $montant_recu += (int) $paiement->paiementMontant;
                if(!$paiement->validate){
                    $calul_montant += (int) $paiement->paiementMontant; // Au debut possibilite de rentrer plusieurs paiement sur l'interface
                    $paiement->validate = true;
                }
            }

            if($montant_recu >= $request->montant_loyer){
                // generer la quittance
                $urlQuittance = $this->generateQuittance($request, $paymentArray, $paiement_list);

            }

            // 📌 Mise à jour du paiement
            $up = PaiementsLoyer::where('paiement_id', request('id_loyer'))
                      ->update([
                        "paiement_recu" => $paiement_list,
                        "paiement_etat" => ($montant_recu >= $request->montant_loyer ? PaiementsLoyer::PAYE:PaiementsLoyer::PAIEMENT_PARTIEL),
                        "paiements_url_quittance" => $urlQuittance

                  ]);



            // update Avoir Locataire
           /* $locat = Locataires::where('locat_id', request('id_locataire'))
                      ->update([
                        "locat_avoir" => request('avoir')
                  ]);*/





            // 🏦 Enregistrer l'opération comptable


            $q = new Operations;

            $oper_id = Helper::IDGenerator(new Operations, 'oper_id',config('constants.ID_LENGTH'), config('constants.PREFIX_OPERATION'));

            $q->oper_id=$oper_id;
            $q->oper_sens=config('constants.CREDIT');
            $q->oper_type=Operations::PAIEMENT_LOYER;
            $q->oper_note=($montant_recu >= $request->montant_loyer ? PaiementsLoyer::getEtatPaiement()[PaiementsLoyer::PAYE]: PaiementsLoyer::getEtatPaiement()[PaiementsLoyer::PAIEMENT_PARTIEL])/*.$has_avoir*/;
            $q->oper_montant=$calul_montant;
            $q->oper_id_bail=request('id_bail');
            $q->oper_user=$user->username;


            $q->save();



            if($up){
                $rep = [
                    "code" => 0,
                    "message" => "OK",
                    "paiementDetails" => json_encode($paiement_list),
                    "resultPaiementSolde" => json_encode($resultPaiementSolde)
                ];
            }else{
                $rep = [
                    "code" => 1,
                    "message" => "KO"
                ];
            }

            DB::commit();

            return response($rep);

          } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Erreur lors du paiement -> '.$e->getMessage()], 500);
        }

    }

    public function payerLoyer(Request $request)
    {
        DB::beginTransaction();
        try {
            $montantAPayer = $request->mntApayer;
            $locataireId = $request->id_locataire;

            // Récupérer les versements actifs (avec du solde dispo)
            $versements = VersementLocataire::where('locataire_id', $locataireId)
                            ->where('solde_disponible', '>', 0)
                            ->where('statut', 'actif') // Seuls les versements actifs
                            ->orderBy('date_versement', 'asc')
                            ->get();

            foreach ($versements as $versement) {
                if ($montantAPayer <= 0) break;

                // Vérifier combien on peut prélever sur ce versement
                $montantPreleve = min($montantAPayer, $versement->solde_disponible);

                // Mettre à jour le versement
                $versement->solde_utilise += $montantPreleve;
                $versement->solde_disponible -= $montantPreleve;

                // Si le solde est épuisé, on met le statut "épuisé"
                if ($versement->solde_disponible == 0) {
                    $versement->statut = 'epuise';
                }

                $versement->save();

                // Réduire le montant à payer
                $montantAPayer -= $montantPreleve;
            }

            // Mettre à jour le solde total du locataire
            $totalSolde = VersementLocataire::where('locataire_id', $locataireId)
                            ->where('statut', 'actif') // Ne prendre en compte que les actifs
                            ->sum('solde_disponible');

            Locataires::where('locat_id', $locataireId)
                     ->update(['locat_avoir' => $totalSolde]);

            DB::commit();
            return ['code'=>0, 'message' => 'Prélevement solde effectué avec succès', 'solde_apres' => $totalSolde, 'mnt_reste_a_payer' => $montantAPayer];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['code'=>1, 'error' => 'Erreur lors du paiement'.$e->getMessage()];
        }
    }


    public function generateRecu($request, $data, $paiementList, $soldePrevele, $fichierJustificatif){
        // Définir l'année actuelle
        $currentYear = date('Y');

        // Définir le chemin du dossier de l'année en cours
        $path = strtolower("documents_paiements/{$currentYear}/{$request->input('id_bail')}/recus");
        $directoryPath = public_path($path);

        // Créer le dossier s'il n'existe pas
        if (!File::exists($directoryPath)) {
            File::makeDirectory($directoryPath, 0755, true);
        }

        // Générer le nom de fichier avec la date et l'heure actuelles
        $timestamp = date('Y-m-d_H-i-s');
        $fileName = strtolower("recu_" . strval($request->input('id_bail')) . "_{$timestamp}.pdf");

        $lastKey = array_key_last($paiementList);
        $ref = strtoupper(bin2hex(random_bytes(8)));

        $paiementList[$lastKey]->url_recu = "/{$path}/{$fileName}";
        $paiementList[$lastKey]->justificatif = $fichierJustificatif;
        $paiementList[$lastKey]->paiementMontant +=   (float) $soldePrevele;
        $paiementList[$lastKey]->paiementType .= ($soldePrevele == 0 ?'':' / Solde utilisé: '.$soldePrevele);
        $paiementList[$lastKey]->reference = $ref;

        // Chemin complet du fichier
        $filePath = "{$directoryPath}/{$fileName}";

        $data['pathFile'] = $path;
        $data['fichier'] = "/{$path}/{$fileName}";
        $data['montant_payer'] = $paiementList[$lastKey]->paiementMontant; //$request->input('montant_payer');
        $data['paiementType'] = $paiementList[$lastKey]->paiementType;
        $data['montant_payer_en_lettre'] = $request->input('montant_payer_en_lettre');
        $data['date_paiement'] = Carbon::now()->format('d/m/Y H:i'); //$request->date_paiement;
        $data['agence_id'] = auth()->user()->agence_id;
        $data['ref'] = $ref;



        // Supprimer le fichier s'il existe déjà
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        // Générer le PDF et le sauvegarder
        $pdf = $this->pdfService->quittanceLoyer(
            config('common.pdf.recu_loyer.template'),
            $data,
            $filePath
        );

        // verifier s'il y'a surplus sur le montant a payer

        if($paiementList[$lastKey]->paiementMontant > $request->mntApayer){
            // ajouter un versement

            $surplus = (float) $paiementList[$lastKey]->paiementMontant - (float) $request->mntApayer;
            // ajouter un new versement
            $this->ajouterVersement($request, $surplus, $paiementList[$lastKey]->paiementType);
        }

        // Preparer le mail
        MailEnAttente::create([
            'email_destinataire' => $data['locat_email'],
            'email_cc' => $data['proprio_email'],
            'sujet' => 'Paiement Loyer',
            'action'=> 'Reçu Paiement Loyer',
            'contenu_html' => '',
            'contenu_text' => '',
            'fichier_joint' => $filePath, // Exemple : 'recu/RECU123456.pdf' si un fichier existe
            'etat' => 'en_attente',
            'template' => 'recu',
            'data' => json_encode($data)
        ]);



        if (!$pdf) {
            throw new PrintException("Pdf has not been generated");
        }

        return $paiementList;
    }


     /**
     * Ajouter un nouveau versement
     */
    public function ajouterVersement($request, $mnt, $typeVersement)
    {
        DB::beginTransaction();
        try {
            $versement = VersementLocataire::create([
                'locataire_id' => $request->id_locataire,
                'montant' => $mnt,
                'solde_utilise' => 0,
                'solde_disponible' => $mnt,
                'mode_paiement' => $typeVersement,
                'statut' => 'actif', // Par défaut, un nouveau versement est actif
                'date_versement' => now(),
            ]);

            // Mettre à jour le solde total du locataire
            $totalSolde = VersementLocataire::where('locataire_id', $request->id_locataire)
                            ->where('statut', 'actif')
                            ->sum('solde_disponible');

            Locataires::where('locat_id', $request->id_locataire)
                     ->update(['locat_avoir' => $totalSolde]);

            DB::commit();
            return response()->json(['message' => 'Versement ajouté avec succès', 'solde_total' => $totalSolde]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Erreur lors de l\'ajout du versement'], 500);
        }
    }

    public function generateQuittance($request, $data, $paiementList){
        // Définir l'année actuelle
        $currentYear = date('Y');

        // Définir le chemin du dossier de l'année en cours
        $path = strtolower("documents_paiements/{$currentYear}/{$request->input('id_bail')}/quittances");
        $directoryPath = public_path($path);

        // Créer le dossier s'il n'existe pas
        if (!File::exists($directoryPath)) {
            File::makeDirectory($directoryPath, 0755, true);
        }

        // Générer le nom de fichier avec la date et l'heure actuelles
        $timestamp = date('Y-m-d_H-i-s');
        $fileName = strtolower("quittance_" . strval($request->input('id_bail')) . "_{$timestamp}.pdf");

        // Chemin complet du fichier
        $filePath = "{$directoryPath}/{$fileName}";

        $lastKey = array_key_last($paiementList);

        //$paiementList[$lastKey]->url_recu = "/{$path}/{$fileName}";
        //$paiementList[$lastKey]->justificatif = $fichierJustificatif;

        // Chemin complet du fichier
        $filePath = "{$directoryPath}/{$fileName}";
        $ref = strtoupper(bin2hex(random_bytes(8)));

        $data['pathFile'] = $path;
        $data['montant_payer'] = $paiementList[$lastKey]->paiementMontant; //$request->input('montant_payer');
        $data['paiementType'] = $paiementList[$lastKey]->paiementType;
        $data['montant_payer_en_lettre'] = $request->input('montant_payer_en_lettre');
        $data['date_paiement'] =  Carbon::now()->format('d/m/Y H:i'); //$request->date_paiement;
        $data['agence_id'] = auth()->user()->agence_id;
        $data['ref'] = $ref;

        $data['pathFile'] = $path;
        $data['fichier'] = "/{$path}/{$fileName}";


        // Supprimer le fichier s'il existe déjà
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        // Générer le PDF et le sauvegarder
        $pdf = $this->pdfService->quittanceLoyer(
            config('common.pdf.quittance_loyer.template'),
            $data,
            $filePath
        );

        // Preparer le mail
        MailEnAttente::create([
            'email_destinataire' => $data['locat_email'],
            'email_cc' => $data['proprio_email'],
            'sujet' => 'Quittance Loyer',
            'action'=> 'Quittance Loyer',
            'contenu_html' => '',
            'contenu_text' => '',
            'fichier_joint' => $filePath,
            'etat' => 'en_attente',
            'template' => 'quittance',
            'data' => json_encode($data)
        ]);



        if (!$pdf) {
            throw new PrintException("Pdf has not been generated");
        }

        return "/{$path}/{$fileName}";
    }

    public function ajoutPaiementLoyerManuel(Request $request){
       $user = Auth::user();

       $paiement_id = Helper::IDGenerator(new PaiementsLoyer, 'paiement_id',config('constants.ID_LENGTH'), config('constants.PREFIX_PAIEMENT_LOYER'));
       // verifier si le mois n'est pas encore generé
       $checkMois = PaiementsLoyer::where('paiement_mois_location', $request->moisLoyer)
                    ->where('paiement_bail_id', $request->bail_id)
                    ->first();

        if(!$checkMois){
            $creation = PaiementsLoyer::create([
                'paiement_id' => $paiement_id,
                'paiement_bail_id' => $request->bail_id,
                'paiement_montant' => $request->montantLoyer,
                'paiement_mois_location' => $request->moisLoyer,
                'paiement_etat' => false,
                'paiement_user' => $user->username,
            ]);

            // preparer l'envoi de mail aux administrateurs
            $data['ref'] = $paiement_id;
            $data['bail_id'] = $request->bail_id;
            $data['local_id'] = $request->local_id;
            $data['montantLoyer'] = $request->montantLoyer;
            $data['paiement_mois_location'] = $request->moisLoyer;
            $data['paiement_montant'] = $request->paiement_montant;
            $data['agent'] = $user->username;
            $data['nomAgent'] = $user->name;
            $data['details'] = $request->details;
            $data['agence_id'] = auth()->user()->agence_id;
            $data['date'] =  Carbon::now()->format('d/m/Y H:i');

            MailEnAttente::create([
                'email_destinataire' => config('common.emailNotif'),
                'email_cc' => '',
                'sujet' => "Ajout manuel d'un nouveau paiement",
                'action'=> 'Génération paiement manuel loyer',
                'contenu_html' => '',
                'contenu_text' => '',
                'fichier_joint' => '',
                'etat' => 'en_attente',
                'template' => 'generationPaiementManuel',
                'data' => json_encode($data)
            ]);

            if($creation){
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
        }else{
            $rep = [
                    "code" => 1,
                    "message" => "Attention Doublon!!!! ce mois existe déjà."
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
   public function destroyPaiement($id)
    {
        $paiement = PaiementsLoyer::where('paiement_id', $id)->first();

        if ($paiement) {
            $paiement->delete();

            return response([
                "code" => 0,
                "message" => "Le paiement a été supprimé avec succès."
            ]);
        } else {
            return response([
                "code" => 1,
                "message" => "Le paiement est introuvable."
            ]);
        }
    }


   public function ajoutOperation(Request $request){
  
    $user = Auth::user();

    $paiement_list = json_decode($request->paiements);
    
    $calul_montant = 0;
    $montant_recu = 0;

    $has_avoir = "";

    if(request('avoir') > 0){
        $has_avoir =' - Avoir ('.request('avoir').')';
    }

    // generer la quittance
        // get data
        $paymentdata = PaiementsLoyer::leftJoin('bails', 'bails.bail_id', '=', 'paiements_loyers.paiement_bail_id')->leftJoin('proprietaires', 'bails.bail_proprio', '=', 'proprietaires.proprio_id')->leftJoin('locataires', 'bails.bail_locataire', '=', 'locataires.locat_id')->leftJoin('biens', 'bails.bail_bien', '=', 'biens.bien_id')
        ->select('paiements_loyers.*', 'bails.bail_proprio', 'proprietaires.*','biens.*', 'locataires.*','bails.bail_locataire', 'bails.bail_local', 'bails.bail_montant_ht')->where('paiement_id', request('id_loyer'))->first();

        if($paymentdata) {
            $paymentArray = $paymentdata->toArray(); // Si c'est un modèle Eloquent

        } else {
            $paymentArray = []; // Renvoie un tableau vide si aucun résultat
        }

        // Définir l'année actuelle
        $currentYear = date('Y');

        // Définir le chemin du dossier de l'année en cours
        $path = "uploads/{$currentYear}/quittanceLoyer";
        $directoryPath = public_path($path);

        // Créer le dossier s'il n'existe pas
        if (!File::exists($directoryPath)) {
            File::makeDirectory($directoryPath, 0755, true);
        }

        // Générer le nom de fichier avec la date et l'heure actuelles
        $timestamp = date('Y-m-d_H-i-s');
        $fileName = "quittance_loyer_" . strval($request->input('id_loyer')) . "_{$timestamp}.pdf";

        // Chemin complet du fichier
        $filePath = "{$directoryPath}/{$fileName}";

        $paymentArray['pathFile'] = $path;

        // Supprimer le fichier s'il existe déjà
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        // Générer le PDF et le sauvegarder
        $pdf = $this->pdfService->quittanceLoyer(
            config('common.pdf.quittance_loyer.template'),
            $paymentArray,
            $filePath
        );

        if (!$pdf) {
            throw new PrintException("Pdf has not been generated");
        }
    
        foreach($paiement_list as $paiement){
            $montant_recu += (int) $paiement->paiementMontant;
            if(!$paiement->validate){
                $calul_montant += (int) $paiement->paiementMontant;
                $paiement->validate = true;
            }
        }

        $lastKey = array_key_last($paiement_list);

        $paiement_list[$lastKey]->url_quittance = "/{$path}/{$fileName}";

        // Update paiement
        $up = PaiementsLoyer::where('paiement_id', request('id_loyer'))
                  ->update([
                    "paiement_recu" => $paiement_list,
                    "paiement_etat" => ($montant_recu >= $request->montant_loyer ? PaiementsLoyer::PAYE:PaiementsLoyer::PAIEMENT_PARTIEL)
              ]);

        // update Avoir Locataire
        $locat = Locataires::where('locat_id', request('id_locataire'))
                  ->update([
                    "locat_avoir" => request('avoir')
              ]);

        // Save opération
        try{

            $q = new Operations;

            $oper_id = Helper::IDGenerator(new Operations, 'oper_id',config('constants.ID_LENGTH'), config('constants.PREFIX_OPERATION'));
      
            $q->oper_id=$oper_id;
            $q->oper_sens=config('constants.CREDIT');
            $q->oper_type=Operations::PAIEMENT_LOYER;
            $q->oper_note=($montant_recu >= $request->montant_loyer ? PaiementsLoyer::getEtatPaiement()[PaiementsLoyer::PAYE]: PaiementsLoyer::getEtatPaiement()[PaiementsLoyer::PAIEMENT_PARTIEL]).$has_avoir;
            $q->oper_montant=$calul_montant;
            $q->oper_id_bail=request('id_bail');
            $q->oper_user=$user->username;


            $q->save();

        }catch(\Exceptions $e){
              return response([
                "code" => 1,
                "message" => $e->getMessage()
            ]);
        }

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

    public function ajoutCharge(Request $request){
  
    $user = Auth::user();

     // Save charge
        try{

            $c = new ChargesFrais;

            $charge_id = Helper::IDGenerator(new ChargesFrais, 'charge_id',config('constants.ID_LENGTH'), config('constants.PREFIX_CHARGES_FRAIS'));

            $files = Helper::getFiles($request->TotalFiles, $request, config('constants.PATH_FACTURE'), request('proprio'),  config('constants.PREFIX_CHARGES_FRAIS'));
      
            $c->charge_id=$charge_id;
            $c->charge_type=request('type');
            $c->charge_type_autre=request('type_autre');
            $c->charge_note=request('note');
            $c->charge_date=request('date');
            $c->charge_montant=(double) str_replace(' ', '', request('montant'));
            $c->charge_id_proprio=request('proprio');
            $c->charge_id_bien=request('bien');
            $c->charge_id_local=request('local'); 
            $c->charge_user=$user->username;
            $c->charge_facture=json_encode($files);


            $c->save();

            // Save dans Operation

            $q = new Operations;

            $oper_id = Helper::IDGenerator(new Operations, 'oper_id',config('constants.ID_LENGTH'), config('constants.PREFIX_OPERATION'));
      
            $q->oper_id=$oper_id;
            $q->oper_sens=config('constants.DEBIT');
            $q->oper_type=request('type');
            $q->oper_note=request('note');
            $q->oper_montant=request('montant');
            $q->oper_id_charge=$charge_id;
            $q->oper_user=$user->username;


            $q->save();

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
                'sujet' => '💰 Nouvelle charge enregistrée',
                'action' => 'Ajout d’une charge ou frais',
                'contenu_html' => '',
                'contenu_text' => '',
                'fichier_joint' => '',
                'etat' => 'en_attente',
                'template' => 'nouvelle_charge', // attention au nom du template
                'data' => json_encode($data),
            ]);

            // Lorsqu'un nouveau locataire est créé
           NotificationService::creerNotification(
            'creation', // Type d’action
            '💸 Charge enregistrée', // Titre de la notification
            "Une nouvelle charge a été enregistrée pour le propriétaire.", // Message
            [
                'proprio_id' => request('proprio'),
                'proprio_nom' => request('proprio_nom'),
                'proprio_prenom' => request('proprio_prenom'),
                'montant' => request('montant'),
                'type_charge' => request('type'),
            ]
        );



        }catch(\Exceptions $e){
              return response([
                "code" => 1,
                "message" => $e->getMessage()
            ]);
        }

        if($c){
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

    public function getBienByProprio($id){
               
        $listBiens = Biens::where("bien_proprio", $id)->get();
        if($listBiens){
            return response([
                "code" => 0,
                "data" => $listBiens
            ]); 
        }
        
    }

     /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
        public function destroyCharge($id)
        {
            $decharge = ChargesFrais::where('charge_id',$id)->first();

            // Delete all files
            Helper::deleteFiles(config('constants.PREFIX_CHARGES_FRAIS'), $decharge["charge_facture"]);

            // Supprimer les operation de la charge associés
            $operation = Operations::where('oper_id_charge',$id)->first();

            if($operation) $operation->delete();


            $decharge->delete();

            if($decharge){
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
