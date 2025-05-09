<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Locataires;

class PinLocataireVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
       // Si PIN non vérifié
        if (!session('pin_verified')) {
            // Autoriser l'accès uniquement à la route de saisie PIN (POST + GET)
            if ($request->is('badge/locataire/*') && $request->method() === 'POST') {
                return $next($request); // On laisse passer pour soumettre le formulaire PIN
            }

            if ($request->is('badge/locataire/*')) {
                // Afficher le formulaire
                return response()->view('badges.locataire', [
                    'locataire' => Locataires::where('locat_id', $request->route('locataire_id'))->first(),
                    'paiements' => []
                ]);
            }

            return redirect('/'); // Sinon on bloque
        }

        // Vérification de l'expiration du PIN
        $verifiedAt = session('pin_verified_at');
        $maxMinutes = 1;

        if ($verifiedAt && now()->diffInMinutes($verifiedAt) > $maxMinutes) {
            session()->forget(['pin_verified', 'pin_verified_at']);
            return redirect()->route('badgeLocataire.show', ['locataire_id' => $request->route('locataire_id')])
                ->with('error', 'Session expirée. Veuillez saisir de nouveau votre code PIN.');
        }

        return $next($request);
    }
}
