<?php

use App\Models\Section;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

if (! function_exists('getUserInfos')) {
    function getUserInfos($id = null) {
        // This method will return all the information from all tables about the authenticated user
        $id = $id ?? auth()->id();
        return User::where('id', $id)->first();
    }
}

if (! function_exists('getAllSectionsNames')) {
    function getAllSectionsNames() {
        $contact = Section::groupBy('name');
        return $contact->pluck('name');
    }
}

if (! function_exists('getAllSections')) {
    function getAllSections() {
        return DB::table('sections')->get();
    }
}

if (! function_exists('currentSubscription')) {
    function currentSubscription() {
        return auth()->user()->subscriptions()->first();
    }
}

if (!function_exists('generateQrCode')) {
    function generateQrCode($url, $size)
    {
        // Générer le QR code en SVG
        $svg = QrCode::format('svg')->size($size)->generate($url);

        // Supprimer l'en-tête XML
        $svgSansEntete = preg_replace('/<\?xml.*?\?>/', '', $svg);

        return $svgSansEntete;
    }
}

if(!function_exists('numberToLetter')){
    function numberToLetter($nombre) {
        if (!is_numeric($nombre)) {
            return "Nombre non valide";
        }

        $nb = (int) $nombre;
        if ($nb != $nombre) {
            return "Nombre avec virgule non géré.";
        }

        if ($nb > 999999999999999) {
            return "Dépassement de capacité";
        }

        $unites = ["", "un", "deux", "trois", "quatre", "cinq", "six", "sept", "huit", "neuf"];
        $dizaines = ["", "dix", "vingt", "trente", "quarante", "cinquante", "soixante", "soixante-dix", "quatre-vingt", "quatre-vingt-dix"];

        if ($nb == 0) {
            return "zéro";
        }

        if ($nb < 10) {
            return $unites[$nb];
        }

        if ($nb < 20) {
            $speciaux = [10 => "dix", 11 => "onze", 12 => "douze", 13 => "treize", 14 => "quatorze",
                         15 => "quinze", 16 => "seize", 17 => "dix-sept", 18 => "dix-huit", 19 => "dix-neuf"];
            return $speciaux[$nb];
        }

        if ($nb < 100) {
            $dizaine = intdiv($nb, 10);
            $unite = $nb % 10;
            if ($unite == 1 && ($dizaine == 1 || $dizaine == 7 || $dizaine == 9)) {
                return $dizaines[$dizaine] . "-et-" . $unites[$unite];
            }
            return trim($dizaines[$dizaine] . ($unite ? "-" . $unites[$unite] : ""));
        }

        if ($nb < 1000) {
            $centaine = intdiv($nb, 100);
            $reste = $nb % 100;
            $cent = $centaine == 1 ? "cent" : $unites[$centaine] . " cent" . ($reste == 0 ? "s" : "");
            return trim($cent . " " . numberToLetter($reste));
        }

        if ($nb < 1000000) {
            $millier = intdiv($nb, 1000);
            $reste = $nb % 1000;
            $mille = $millier == 1 ? "mille" : numberToLetter($millier) . " mille";
            return trim($mille . " " . numberToLetter($reste));
        }

        if ($nb < 1000000000) {
            $million = intdiv($nb, 1000000);
            $reste = $nb % 1000000;
            $mil = $million == 1 ? "un million" : numberToLetter($million) . " millions";
            return trim($mil . " " . numberToLetter($reste));
        }

        if ($nb < 1000000000000) {
            $milliard = intdiv($nb, 1000000000);
            $reste = $nb % 1000000000;
            $mld = $milliard == 1 ? "un milliard" : numberToLetter($milliard) . " milliards";
            return trim($mld . " " . numberToLetter($reste));
        }

        return "Nombre trop grand";
    }

}
