<?php

namespace App\Http\Controllers;

use App\Models\Locataires;
use App\Models\Proprietaires;
use App\Models\Agence;
use App\Models\PaiementsLoyer;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

class BadgeController extends Controller
{
   public function show($locataire_id, Request $request)
    {
        $locataire = Locataires::where('locat_id', $locataire_id)->firstOrFail();

        $agence = Agence::find($locataire->agence_id);

        if ($request->isMethod('post')) {
            $enteredPin = $request->input('pin');

            // Initialiser compteur si pas encore fait
            if (!session()->has('pin_attempts')) {
                session(['pin_attempts' => 0]);
            }

              if ($locataire->pin !== $enteredPin) {
                // Incrémenter
                session(['pin_attempts' => session('pin_attempts') + 1]);

                // Si 3 erreurs → "deconnexion forcée"
                if (session('pin_attempts') >= 3) {
                    session()->forget('pin_attempts');
                    session()->forget('pin_verified');
                    return redirect()->route('badgeLocataire.show', ['locataire_id' => $locataire_id])
                            ->with('error', 'Trop de tentatives échouées. Merci de rescanner le QR Code.');
                }

                return back()->with('error', 'Code PIN incorrect');
            }

            // Si OK, reset le compteur
            session(['pin_verified' => true]);
            session()->put('pin_verified_at', now());
            session()->forget('pin_attempts');

            return redirect()->route('badgeLocataire.show', ['locataire_id' => $locataire_id]);
        }

        if (!session('pin_verified')) {
            return view('badges.locataire', [
                'locataire' => $locataire,
                'paiements' => [],
            ]);
        }




        $paiements = DB::table('paiements_loyers')->join('bails', 'paiements_loyers.paiement_bail_id', '=', 'bails.bail_id')
            ->join('biens', 'bails.bail_bien', '=', 'biens.bien_id')->join('locataires', 'bails.bail_locataire', '=', 'locataires.locat_id')->where('locataires.locat_id', $locataire->locat_id)
            ->get()
            ->map(function($paiement){
                $recu = json_decode($paiement->paiement_recu, true) ?? [];
                $totalPaye = collect($recu)->sum('paiementMontant');

                return (object)[
                    'paiement_mois_location' => $paiement->paiement_mois_location,
                    'total_paye' => $totalPaye,
                    'statut' => $paiement->paiement_etat == 3 ? 'payé' : ($paiement->paiement_etat == 2 ? 'partiel' : 'impayé'),
                ];
            });

        return view('badges.locataire', [
            'locataire' => $locataire,
            'paiements' => $paiements,
            'agence' => $agence,
        ]);
    }


    public function showProprio($proprietaire_id, Request $request)
    {
        $proprietaire = Proprietaires::where('proprio_id', $proprietaire_id)->firstOrFail();

        $agence = Agence::find($proprietaire->agence_id);

        if ($request->isMethod('post')) {
            $enteredPin = $request->input('pin');

            // Initialiser compteur si pas encore fait
            if (!session()->has('pin_attempts')) {
                session(['pin_attempts' => 0]);
            }

              if ($proprietaire->pin !== $enteredPin) {
                // Incrémenter
                session(['pin_attempts' => session('pin_attempts') + 1]);

                // Si 3 erreurs → "deconnexion forcée"
                if (session('pin_attempts') >= 3) {
                    session()->forget('pin_attempts');
                    session()->forget('pin_verified');
                    return redirect()->route('badgeProprietaire.show', ['proprietaire_id' => $proprietaire_id])
                            ->with('error', 'Trop de tentatives échouées. Merci de rescanner le QR Code.');
                }

                return back()->with('error', 'Code PIN incorrect');
            }

            // Si OK, reset le compteur
            session(['pin_verified' => true]);
            session()->put('pin_verified_at', now());
            session()->forget('pin_attempts');

            return redirect()->route('badgeProprietaire.show', ['proprietaire_id' => $proprietaire_id]);
        }

        if (!session('pin_verified')) {
            return view('badges.proprietaire', [
                'proprietaire' => $proprietaire,
                'versements' => [],
            ]);
        }




        $versements = DB::table('paiements_loyers')->join('bails', 'paiements_loyers.paiement_bail_id', '=', 'bails.bail_id')
            ->join('biens', 'bails.bail_bien', '=', 'biens.bien_id')->join('proprietaires', 'proprietaires.proprio_id', '=', 'biens.bien_proprio')->join('locataires', 'bails.bail_locataire', '=', 'locataires.locat_id')->where('proprietaires.proprio_id', $proprietaire->proprio_id)
            ->get()
            ->map(function($versement){
                $recu = json_decode($versement->paiement_recu, true) ?? [];
                $totalPaye = collect($recu)->sum('paiementMontant');

                return (object)[
                    'paiement_mois_location' => $versement->paiement_mois_location,
                    'total_paye' => $totalPaye,
                    'statut' => $versement->paiement_etat == 3 ? 'payé' : ($versement->paiement_etat == 2 ? 'partiel' : 'impayé'),
                ];
            });

        return view('badges.proprietaire', [
            'proprietaire' => $proprietaire,
            'versements' => $versements,
            'agence' => $agence,
        ]);
    }

}
