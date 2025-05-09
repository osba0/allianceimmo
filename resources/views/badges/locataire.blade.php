<?php
  /**
 * @var array $data
 */
//$mainLogo = Settings::getValue('adminSettings:mainLogo');
use Carbon\Carbon;
//use App\Models\Agence;

//$agence = Agence::where("id",auth()->user()->agence_id)->first()->toArray();
$logoPath = public_path('assets/images/logo_ab_immo_rounded_1.png');

?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Badge Locataire</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background: #f2f2f2; }
    .card { margin-top: 30px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
    .header-logo img { max-height: 60px; }
  </style>
</head>
<body>

  @if (session('message'))
    <div class="alert alert-info text-center mt-2">
      {{ session('message') }}
    </div>
  @endif
  @if (!session('pin_verified'))
<div class="container d-flex flex-column justify-content-center align-items-center" style="height: 100vh;">
  <div class="card p-4 shadow" style="width: 100%; max-width: 400px;">
    <h4 class="text-center mb-3">🔒 Entrez votre code PIN</h4>

    <form method="POST" action="{{ url()->current() }}">
      @csrf
      <div class="form-group">
        <input type="password" name="pin" maxlength="4" class="form-control text-center" placeholder="Votre code PIN" required autofocus>
      </div>
      <button type="submit" class="btn btn-primary btn-block">Valider</button>
    </form>

    @if (session('error'))
      <div class="alert alert-danger mt-3 text-center">{{ session('error') }}</div>
    @endif
  </div>
  <div class="text-center mt-4">
      <small>Version 3.1.4 by SB DIGITAL SOLUTIONS </small>
    </div>
</div>
@else
<!-- Afficher ici le badge et les paiements -->
<div class="container">

  <!-- Logo -->
  <div class="text-center my-3 header-logo">
    <div>
       @if(file_exists($logoPath))
            <img src="/assets/images/logo_ab_immo_rounded_1.png" class="logo" alt="Logo">
        @else
            <img src="https://allianceimmo.net/assets/images/logo-login.png" class="logo" alt="Default Logo"/>
        @endif
    </div>
    <div> <strong>{{ $agence['agence_nom'] }}</strong></div>

    <div class="text-center mt-3">
         <a href="{{ route('badgeLocataire.logout', ['locataire_id' => $locataire->locat_id]) }}" onclick="return confirm('Êtes-vous sûr de vouloir vous déconnecter ?')" class="btn btn-outline-danger btn-sm">
        🔓 Se déconnecter
      </a>
    </div>
  </div>

  <!-- Infos -->
  <div class="card p-3">
    <h4 class="text-center mb-4">Bonjour {{ $locataire->locat_prenom }} {{ $locataire->locat_nom }}</h4>

    <h5 class="text-primary text-center">📋 Vos Paiements 6 derniers mois:</h5>

    <div class="table-responsive mt-4">
      <table class="table table-striped table-bordered">
        <thead class="thead-light">
          <tr>
            <th>Mois</th>
            <th>Montant payé</th>
            <th>Statut</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($paiements as $paiement)

            <tr>
              <td>{{ \Carbon\Carbon::parse($paiement->paiement_mois_location)->format('m/Y') }}
              </td>
              <td>{{ number_format($paiement->total_paye, 0, ',', ' ') }} FCFA</td>
              <td>
                @if ($paiement->statut == 'payé')
                  <span class="badge">✅ Payé</span>
                @elseif ($paiement->statut == 'partiel')
                  <span class="badge">⚠️ Partiel</span>
                @else
                  <span class="badge">❌ Impayé</span>
                @endif
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <div class="text-center mt-4">
      <small>Mis à jour le {{ now()->format('d/m/Y à H:i') }}</small>
    </div>
  </div>
   <div class="text-center mt-4">

      <small>Version 3.1.4 by SB DIGITAL SOLUTIONS </small>
    </div>

</div>
@endif



</body>
</html>
