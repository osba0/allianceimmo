<?php
/**
 * @var array $data
 */
//$mainLogo = Settings::getValue('adminSettings:mainLogo');
use Carbon\Carbon;
use App\Models\Local;
use App\Models\Agence;

$json = json_decode($data, true);

$agence = Agence::where("id", $json['agence_id'])->first()->toArray();

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
    <h2 style="background: rgb(38, 50, 77); color: white; text-align: center; padding: 4px 0">🏠 Nouveau propriétaire</h2>
  </div>
  <div style="padding: 0 25px;">


    <p>Bonjour,</p>


    <p>Nous vous informons qu'un nouveau propriétaire a été ajouté avec succès dans la plateforme.</p>

    <p><strong>Détails :</strong></p>
    <ul>
        <li><strong>Nom :</strong> {{ $json['proprio_nom'] }}</li>
        <li><strong>Prénom :</strong> {{ $json['proprio_prenom'] }}</li>
        <li><strong>Email :</strong> {{ $json['proprio_email'] }}</li> <!--awaba35-->
        <li><strong>Téléphone :</strong> {{ $json['proprio_tel'] }}</li> <!--Awa773101856 newpwd: 2024-->
        <li><strong>Crée par :</strong> {{ $json['created_by'] }}</li>
    </ul>

    <a href="{{ env('APP_URL')  }}" class="btn" style="color: rgb(38, 50, 77)">🔎 Voir sur la plateforme</a>

    <p>Merci de vérifier et de compléter les informations si nécessaire.</p>
    <strong>{{$agence['agence_nom']}}</strong></p>

  </div>
  <div class="footer" style="padding: 5px 25px; font-size: 13px; margin-top: 20px; color: #777; border: 1px solid #dedede; text-align: center;">
    Ceci est un message automatique. Merci de ne pas y répondre directement.
  </div>
</body>
</html>
