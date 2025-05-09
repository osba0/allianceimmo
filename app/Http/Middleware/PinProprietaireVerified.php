<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Proprietaires;

class PinProprietaireVerified
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
            if ($request->is('badge/proprietaire/*') && $request->method() === 'POST') {
                return $next($request); // On laisse passer pour soumettre le formulaire PIN
            }

            if ($request->is('badge/proprietaire/*')) {
                // Afficher le formulaire
                return response()->view('badges.proprietaire', [
                    'proprietaire' => Proprietaires::where('proprio_id', $request->route('propritaire_id'))->first(),
                    'versements' => []
                ]);
            }

            return redirect('/'); // Sinon on bloque
        }

        // Vérification de l'expiration du PIN
        $verifiedAt = session('pin_verified_at');
        $maxMinutes = 1;

        if ($verifiedAt && now()->diffInMinutes($verifiedAt) > $maxMinutes) {
            session()->forget(['pin_verified', 'pin_verified_at']);
            return redirect()->route('badgeProprietaire.show', ['propritaire_id' => $request->route('propritaire_id')])
                ->with('error', 'Session expirée. Veuillez saisir de nouveau votre code PIN.');
        }

        return $next($request);
    }
}
