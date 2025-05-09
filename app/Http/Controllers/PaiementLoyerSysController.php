<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PaiementLoyersSys;
use App\Models\Bails;
use Carbon\Carbon;
use Auth;

class PaiementLoyerSysController extends Controller
{
     public function index()
    {
        return view('paiement_loyers/index');
    }

    public function listing(Request $request)
    {
        $paginate = $request->input('paginate', 10);
        $locataireId = $request->input('locataire_id');
        $bailId = $request->input('bail_id');
        $statut = $request->input('statut');
        $dateDebut = $request->input('date_debut');
        $dateFin = $request->input('date_fin');

        $query = PaiementLoyersSys::with(['bail.locataire', 'user'])
            ->orderBy('date_paiement', 'desc');

        // Filtre par locataire
        if ($locataireId) {
            $query->whereHas('bail', function ($q) use ($locataireId) {
                $q->where('locataire_id', $locataireId);
            });
        }

        // Filtre par bail
        if ($bailId) {
            $query->where('bail_id', $bailId);
        }

        // Filtre par statut
        if ($statut) {
            $query->where('statut', $statut);
        }

        // Filtre par période de paiement
        if ($dateDebut && $dateFin) {
            $query->whereBetween('periode_paiement', [$dateDebut, $dateFin]);
        } elseif ($dateDebut) {
            $query->where('periode_paiement', '>=', $dateDebut);
        } elseif ($dateFin) {
            $query->where('periode_paiement', '<=', $dateFin);
        }

        $paiements = $query->paginate($paginate);

        return response()->json($paiements);
    }


    public function store(Request $request)
    {
        $request->validate([
            'bail_id' => 'required|exists:baux,id',
            'montant' => 'required|numeric|min:0',
            'mode_paiement' => 'required|in:espèces,virement,chèque,mobile_money',
            'periode_paiement' => 'required|date',
            'statut' => 'required|in:partiel,complet,avance,arriéré',
        ]);

        $paiement = PaiementLoyerSys::create([
            'bail_id' => $request->bail_id,
            'user_id' => Auth::id(),
            'montant' => $request->montant,
            'date_paiement' => now(),
            'mode_paiement' => $request->mode_paiement,
            'periode_paiement' => $request->periode_paiement,
            'statut' => $request->statut,
            'solde' => $request->solde ?? 0,
            'penalite' => $request->penalite ?? 0,
            'reference' => 'PAY-' . strtoupper(uniqid()),
        ]);

        return response()->json(['message' => 'Paiement enregistré avec succès.', 'paiement' => $paiement]);
    }

    public function generateLoyers()
    {
        $baux = Bails::all();
        foreach ($baux as $bail) {
            PaiementLoyerSys::create([
                'bail_id' => $bail->id,
                'montant' => $bail->montant_loyer,
                'date_paiement' => now(),
                'periode_paiement' => now()->startOfMonth(),
                'statut' => 'arriéré',
                'solde' => $bail->montant_loyer,
                'reference' => 'AUTO-' . strtoupper(uniqid()),
                'generated_by_cron' => true,
            ]);
        }
    }
}
