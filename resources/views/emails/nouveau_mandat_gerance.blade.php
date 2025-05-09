<?php
/**
 * @var array $data
 */
//$mainLogo = Settings::getValue('adminSettings:mainLogo');
use Carbon\Carbon;
use App\Models\Local;
use App\Models\Agence;
use App\Models\Biens;

$json = json_decode($data, true);
$dateDebut = Carbon::createFromFormat('Y-m-d', $json['mandat_date_debut'])
        ->format('d/m/Y');
$dateFin = Carbon::createFromFormat('Y-m-d', $json['mandat_date_fin'])
        ->format('d/m/Y');


$agence = Agence::where("id", $json['agence_id'])->first()->toArray();

$bien = Biens::where("bien_id", $json['bien_id'])->first()->toArray();

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
    <h2 style="background: rgb(38, 50, 77); color: white; text-align: center; padding: 4px 0">📄 Mandat de Gérance</h2>
  </div>
  <div style="padding: 0 25px;">


    <p>Bonjour {{ $json['proprio_nom'] }} {{ $json['proprio_prenom'] }},</p>


     <p>
      Nous vous remercions pour la confiance accordée à notre agence <strong>{{ $agence['agence_nom'] }}</strong>.
    </p>

      <p>
      Veuillez trouver ci-joint le <strong>mandat de gérance</strong> relatif au bien situé à :
      <br><strong>{{ $bien['bien_nom'] }}, {{ $bien['bien_adresse'] }} {{ $bien['bien_numero'] }}, {{ $bien['bien_ville'] }} {{ $bien['bien_pays'] }}</strong>.
    </p>

    <p>
      📅 Période de validité : <strong>{{ $dateDebut }}</strong> au <strong>{{ $dateFin }}</strong>.
    </p>

    <p>
      Pour toute question ou précision, notre équipe reste à votre disposition.
    </p>

    <p>
      <a href="{{ env('APP_URL') }}/{{ $json['path_mandat'] }}" target="_blank" class="btn" style="color: rgb(38, 50, 77)">📥 Télécharger le mandat ici</a>
    </p>

    <p>Cordialement,<br>
    <strong>L’équipe {{$agence['agence_nom']}}</strong></p>



  </div>
  <div class="footer" style="padding: 5px 25px; font-size: 13px; margin-top: 20px; color: #777; border: 1px solid #dedede; text-align: center;">
    Ceci est un message automatique. Merci de ne pas y répondre directement.
  </div>
</body>
</html>
