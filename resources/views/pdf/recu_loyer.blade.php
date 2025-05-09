<?php
/**
 * @var array $data
 */
//$mainLogo = Settings::getValue('adminSettings:mainLogo');
use Carbon\Carbon;
use App\Models\Local;
use App\Models\Agence;

$agence = Agence::where("id",auth()->user()->agence_id)->first()->toArray();

$logoPath = public_path('assets/images/logo_ab_immo_rounded_1.png');

$formatDateDebut = Carbon::createFromFormat('Y-m', $paiement_mois_location)
        ->startOfMonth()
        ->format('d/m/Y');
$formatDateFin = Carbon::createFromFormat('Y-m', $paiement_mois_location)
        ->endOfMonth()
        ->format('d/m/Y');
$moisLoyer = Carbon::createFromFormat('Y-m', $paiement_mois_location)->format('M Y');

$locals = Local::leftJoin('biens', 'biens.bien_id', '=', 'locals.bien_id')
    ->select('locals.*', 'bien_nom', 'bien_adresse')
    ->whereIn("local_id", json_decode($bail_local))
    ->groupBy('locals.local_id')
    ->get()
    ->toArray();

function generateReceiptId() {
    return 'REC-' . $strtoupper(bin2hex(random_bytes(6)));
}
?>

<style>
    .main {
        padding: 0;
        margin: 0;
        width: 21cm;
        min-height: 100%;
        box-sizing: border-box;
        /* Arrière-plan avec l'image centrée et semi-transparente */
        /*background-image: url("https://allianceimmo.net/assets/images/logo-login.png");
        background-position: center;
        background-repeat: no-repeat;
        background-size: 50%;
        opacity: 0.1;*/
    }

     /* Logo en arrière-plan, centré et fixe sur chaque page */
    .background-logo {
        position: absolute;
        top: 50%;
        left: 50%;
        width: 50%; /* Ajustez la taille de l'image ici */
        opacity: 0.1; /* Transparence pour effet filigrane */
        transform: translate(-50%, -50%); /* Centre l'image */
        z-index: -1; /* Place derrière le contenu principal */
    }
    .numero{
        border: 1px solid #000;
        width: 50%;
        margin-top: 10px;
    }

    table {
        width: 100%;
        margin-bottom: 20px;
    }

    .table th,
    .table td {
        padding: 8px 5px;
        border: 1px solid #000000;
        border-right: none;
    }
    .table .border-right {
        border-right: 1px solid #000000;
    }
    .table td {
        border-top: none;
    }

    .logo {
        max-width: 100%;
    }
    .infoAgence{

    }
    ul{
        list-style: none;
    }
    .table-footer{
        border-top: 1px solid #000;
    }

    @page {
        margin-top: 4cm;
        margin-bottom: 2cm;
        margin-left: 1cm;
        margin-right: 1cm;
        header: page-header;
        footer: page-footer;
    }
</style>

<body style="font-family: helvetica; font-size: 12px;">
     <!--img src="https://allianceimmo.net/assets/images/logo-login.png" class="background-logo" alt="Background Logo"-->
<div class="main">

    {{-- HEAD --}}
    <htmlpageheader class="header" name="page-header">
        <div class="top-wrapper">
            <table border="0" cellpadding="0" cellspacing="0" style="width:100%; padding: 0 0 5px 0;">
                <tbody>
                <tr>
                    <td style="text-align:left;padding-top:10px; width: 50%">
                        <table class="infoAgence" border="0" cellpadding="0" cellspacing="0">
                            <tr>
                                <td>
                                    @if(file_exists($logoPath))
                                        <img src="assets/images/logo_ab_immo_rounded_1.png" class="logo" alt="Logo">
                                    @else
                                        <img src="https://allianceimmo.net/assets/images/logo-login.png" class="logo" alt="Default Logo"/>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $agence['agence_nom'] }}</strong><br/>
                                    {{ $agence['agence_slogan'] }}<br/>
                                    {{ $agence['agence_adresse'] }}<br/>
                                    Tel: {{ $agence['agence_tel1'] }} ou {{ $agence['agence_tel2'] }}<br/>
                                    {{ $agence['agence_email'] }}
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td style="text-align:center;padding-top:10px;">
                        <h1>Recu de loyer</h1>
                        <table class="numero" border="0" cellpadding="0" cellspacing="0">
                            <tr><td height="5"></td></tr>
                            <tr><td><td><span style="color: red">N°</span> REC-{{ $ref }}</td></tr>
                            <tr><td height="5"></td></tr>
                        </table>
                    </td>
                </tr>
                </tbody>
            </table>

            <div class="line"></div>

        </div>
    </htmlpageheader>
    {{-- /HEAD --}}
    <table border="0" cellpadding="0" cellspacing="0" style="margin-bottom: 0;">
        <tr><td height="10"></td></tr>
     </table>
    <!--table class="table" border="0" cellspacing="0" cellpadding="0">
        <thead>
        <tr>
            <th align="center">Loyer Principal</th>
            <th align="center">TOM 3.6%</th>
            <th align="center">TLV 3.6%</th>
            <th align="center">TVA</th>
            <th align="center">Charges diverses</th>
            <th align="center">Eau</th>
            <th align="center">Timbre</th>
            <th align="center" class="border-right">Total</th>
        </tr>
        </thead>
        <tbody>
            <tr>
                @foreach($locals as $local)
                @php

                $loyerPrincipal =($local['local_prix_loyer'] - $local['local_montant_charge']) *100/(100+$local['local_tom']+$local['local_tlv']+$local['local_tva']+$local['local_timbre']+$local['local_eau_forfait']);

                @endphp
                <td align="center" width="13%">{{ $loyerPrincipal }} F</td>
                <td align="center" width="12%">{{ $loyerPrincipal * $local['local_tom'] / 100 }} F</td>
                <td align="center" width="12%">{{ $loyerPrincipal * $local['local_tlv'] / 100 }} F</td>
                <td align="center" width="12%">{{ $loyerPrincipal * $local['local_tva'] / 100 }}</td>
                <td align="center" width="14%">{{ $local['local_montant_charge'] }} F</td>
                <td align="center" width="12%">{{ $local['local_eau_forfait'] }} F</td>
                <td align="center" width="12%">{{ $local['local_timbre'] }} F</td>
                <td align="center" width="13%" class="border-right">{{ $local['local_prix_loyer'] }} --- F</td>
                @endforeach
            </tr>
        </tbody>
    </table-->

    <table>
        <tbody>

            <tr>
                <td width="30%"><strong>Reçu de</strong></td>
                <td width="3%">:</td>
                <td width="67%">{{ $locat_prenom }} {{ $locat_nom }}</td>
            </tr>
             <tr>
                <td><strong>La somme de:</strong></td>
                <td width="3%">:</td>
                <td>{{ $montant_payer }} F ({{ $montant_payer_en_lettre }})</td>
            </tr>
            <tr>
                <td><strong>Pour </strong></td>
                <td width="3%">:</td>
                <td>Loyer {{ $moisLoyer }}, Période du {{$formatDateDebut}} au {{$formatDateFin}}</td>
            </tr>
             <tr>
                <td><strong>Des locaux qu'il occupe</strong></td>
                <td width="3%">:</td>
                <td>
                    @foreach($locals as $local)
                        {{ $local['local_type_local'] }}, {{ $local['local_nature_local'] }}
                            {{ $local['local_type_location'] }},
                    @endforeach
                </td>
            </tr>
             <tr>
                <td><strong>Immeuble</strong></td>
                <td width="3%">:</td>
                <td>{{ $bien_nom }}, {{ $bien_numero }}</td>
            </tr>
             <tr>
                <td><strong>Propriétaire</strong></td>
                <td width="3%">:</td>
                <td>{{ $proprio_prenom }} {{ $proprio_nom }} - {{ $bail_proprio }}</td>
            </tr>
             <tr>
                <td><strong>Mode de paiment</strong></td>
                <td width="3%">:</td>
                <td>{{ $paiementType }}</td>
            </tr>
            <tr>
                <td><strong>Payé le </strong></td>
                <td width="3%">:</td>
                <td>{{ $date_paiement }}</td>
            </tr>
             <tr>
                <td><strong>Agent</strong></td>
                <td width="3%">:</td>
                <td>{{ auth()->user()->name }} ({{ auth()->user()->username }})</td>
            </tr>
             <tr>
                <td height="50" colspan="3"></td>
            </tr>
            <tr>
                <td align="left" colspan="2">Dakar, le {{ now()->format('d/m/Y H:i:s') }}</td>
                <td align="right">{!! generateQrCode($pathFile, 80) !!}</td>
            </tr>
        </tbody>
    </table>

    <table border="0" cellpadding="0" cellspacing="0" style="margin-top: 80px">
        <tr>
            <td style="font-weight: 600;">
            <i>N.B: Le paiement de la présente n'emporte pas présomption de paiement des termes antérieurs. Cette quittance ou ce reçu annule tous les reçus qui auraient pu être donnés pour acompte versé sur le présent terme. En cas de congé précédemment donné, cette quittance ou ce reçu représenterait l'indemnité d'occupation et ne saurait être considéré comme un titre d'occupation. Sous réserve d'encaissement.</i>
            </td>
        </tr>
    </table>

    {{-- FOOTER --}}
    <htmlpagefooter class="footer" name="page-footer">
         <table class="table-footer" cellspacing="0" cellpadding="0">
            <tbody>
                <tr><td colspan="2" height="8"></td></tr>
                <tr>
                    <td align="left" width="80%">
                        <strong>ALLIANCE BAZICS IMMO</strong>
                        RC: 1254632, Ninea: 12542222, NITI: 256552
                    </td>
                    <td align="right"> Page {PAGENO} / {nbpg}</td>
                </tr>
            </tbody>
        </table>
    </htmlpagefooter>
     {{-- /FOOTER --}}
</div>
</body>
