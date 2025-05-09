<?php
/**
 * @var array $data
 */
//$mainLogo = Settings::getValue('adminSettings:mainLogo');
use Carbon\Carbon;
use App\Models\Local;
use App\Models\Agence;
use App\Models\Biens;
use App\Models\Locataires;

$json = json_decode($data, true);

$dateDebut = $json['location_date_debut'];
$dateFin = $json['location_date_fin'];


$agence = Agence::where("id", $json['agence_id'])->firstOrFail()->toArray();

$bien = Biens::where("bien_id", $json['bien_id'])->firstOrFail()->toArray();

$locataire = Locataires::where("locat_id", $json['locataire_id'])->firstOrFail()->toArray();

$locauxID = json_decode($json['localIds']);

$locaux = Local::whereIn('local_id', $locauxID)->get()->toArray();

$logoPath = public_path('assets/images/logo_ab_immo_rounded_1.png');

?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <style>
    body { font-family: Arial, sans-serif; font-size: 15px; color: #333; }
    .header { background-color: #000; color: white; padding: 10px; text-align: center; }
    .content { padding: 25px; }
    .footer { font-size: 13px; margin-top: 30px; color: #777; }
  </style>
</head>
<body>

  <div class="header">
    <h2 style="background: rgb(38, 50, 77); color: white; text-align: center; padding: 4px 0">❌ Résiliation de votre bail</h2>
  </div>
  <div style="padding: 0 25px;">


    <p>Bonjour {{ $locataire['locat_nom'] }} {{ $locataire['locat_prenom'] }},</p>


    <p>
      Nous vous informons que le bail relatif au bien situé à :
    </p>

    <p><strong>{{ $bien['bien_nom'] }}, {{ $bien['bien_adresse'] }} {{ $bien['bien_numero'] }}, {{ $bien['bien_ville'] }} {{ $bien['bien_pays'] }}</strong>.
    </p>

    <p>
      est résilié à compter du <strong>{{ $json['date_resiliation'] }}</strong>.
    </p>

    <p>Les locaux suivants sont concernés :</p>

    <ul>
      @foreach($locaux ?? [] as $local)
        <li>
          {{ $local['local_type_local'] ?? '' }} -
          {{ $local['local_nature_local'] ?? '' }}
          ({{ number_format($local['local_prix_loyer'] ?? 0, 0, ',', ' ') }} FCFA)
        </li>
      @endforeach
    </ul>

    <p>
      Si vous avez des questions ou besoin de précisions, n'hésitez pas à nous contacter.
    </p>

    <p>Cordialement,<br>
    <strong>L’équipe {{$agence['agence_nom']}}</strong></p>



  </div>
  <div class="footer" style="padding: 5px 25px; font-size: 13px; margin-top: 20px; color: #777; border: 1px solid #dedede; text-align: center;">
    Ceci est un message automatique. Merci de ne pas y répondre directement.
  </div>
</body>
</html>
