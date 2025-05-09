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
$dateDebut = Carbon::createFromFormat('Y-m-d', $json['location_date_debut'])
        ->format('d/m/Y');
$dateFin = Carbon::createFromFormat('Y-m-d', $json['location_date_fin'])
        ->format('d/m/Y');
//var_dump('expression'); die();

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
    <h2 style="background: rgb(38, 50, 77); color: white; text-align: center; padding: 4px 0">📄 Contrat de bail locatif</h2>
  </div>
  <div style="padding: 0 25px;">


    <p>Bonjour {{ $locataire['locat_nom'] }} {{ $locataire['locat_prenom'] }},</p>


    <p>Nous avons le plaisir de vous transmettre votre contrat de bail relatif au bien situé à :</p>

    <p><strong>{{ $bien['bien_nom'] }}, {{ $bien['bien_adresse'] }} {{ $bien['bien_numero'] }}, {{ $bien['bien_ville'] }} {{ $bien['bien_pays'] }}</strong>.
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

    <p><strong>Détails du bail :</strong></p>
    <ul>
      <li><strong>Durée :</strong> {{ $json['duree_bail'] }} an(s)</li>
      <li><strong>📅 Date de début :</strong> {{ $dateDebut }}</li>
      <li><strong>📅 A renouveler le :</strong> {{ $dateFin }}</li>
      <li><strong>Loyer mensuel :</strong> {{ number_format($json['montant_loyer'], 0, ',', ' ') }} FCFA</li>
    </ul>


     <p>📎 Vous trouverez ci-joint le document officiel à signer. Merci de le lire attentivement et de nous retourner un exemplaire signé.</p>

    <p>Pour toute question ou précision, notre équipe reste à votre disposition.</p>

    <p>
      <a href="{{ env('APP_URL') }}/{{ $json['path_bail'] }}" target="_blank" class="btn" style="color: rgb(38, 50, 77)">📥 Télécharger le bail ici</a>
    </p>

    <p>Cordialement,<br>
    <strong>L’équipe {{$agence['agence_nom']}}</strong></p>



  </div>
  <div class="footer" style="padding: 5px 25px; font-size: 13px; margin-top: 20px; color: #777; border: 1px solid #dedede; text-align: center;">
    Ceci est un message automatique. Merci de ne pas y répondre directement.
  </div>
</body>
</html>
