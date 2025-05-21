<?php

use App\Http\Controllers\SettingsController;
use App\Http\Controllers\Auth\ChangePasswordController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProprietairesController;
use App\Http\Controllers\LocatairesController;
use App\Http\Controllers\BiensController;
use App\Http\Controllers\LocalController;
use App\Http\Controllers\MandatGeranceController;
use App\Http\Controllers\BailController;
use App\Http\Controllers\OperationsController;
use App\Http\Controllers\RepresentantController;
use App\Http\Controllers\RapportController;
use App\Http\Controllers\PersonnelController;
use App\Http\Controllers\PreferenceController;
use App\Http\Controllers\CompteController;
use App\Http\Controllers\AgenceController;
use App\Http\Controllers\FilialeController;
use App\Http\Controllers\GlobalController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\BadgeController;
use App\Http\Controllers\VersementsController;
use App\Http\Controllers\TachesAutomatisesController;
use App\Http\Controllers\Rapports\ProprietairesRapport;

use App\Http\Controllers\PaiementLoyerSysController;

use App\Http\Controllers\Template\QuittanceLoyerTemplateController;
use App\Http\Controllers\Template\TemplateController;


use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes(['verify' => true]);
// Private Routes
Route::group(['middleware' => ['auth']], function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::get('/without/breadcrumbs', [App\Http\Controllers\HomeController::class, 'about'])->name('about');

    // Proprio
    Route::group(['middleware' => ['permissionOrRoot:Menu.Proprietaire']], function () {
        Route::get('/proprio/list', [ProprietairesController::class, 'index'])->name('proprio');
        Route::post('/proprio/create', [ProprietairesController::class, 'store']);
        Route::get('/proprio/listing', [ProprietairesController::class, 'listing']);
        Route::post('/proprio/remphoto', [ProprietairesController::class, 'removePhoto']);
        Route::post('/proprio/modify/{id}', [ProprietairesController::class, 'edit']);
        Route::delete('/proprio/delete/{id}', [ProprietairesController::class, 'destroy']);

        // Representants
        Route::post('/representant/create', [RepresentantController::class, 'store']);
        Route::post('/representant/listing', [RepresentantController::class, 'listing']);
        Route::post('/representant/modify/{id}', [RepresentantController::class, 'edit']);
        Route::delete('/representant/delete/{id}', [RepresentantController::class, 'destroy']);

    });


    // Locataires
    Route::group(['middleware' => ['permissionOrRoot:Menu.Locataire']], function () {
        Route::get('/locat/list', [LocatairesController::class, 'index'])->name('locataire');
        Route::post('/locat/create', [LocatairesController::class, 'store']);
        Route::get('/locat/listing', [LocatairesController::class, 'listing']);
        Route::post('/locat/remphoto', [LocatairesController::class, 'removePhoto']);
        Route::post('/locat/modify/{id}', [LocatairesController::class, 'edit']);
        Route::delete('/locat/delete/{id}', [LocatairesController::class, 'destroy']);
    });

    // Bien / Immeuble
    Route::group(['middleware' => ['permissionOrRoot:Menu.Bien']], function () {
        Route::get('/bien/index', [BiensController::class, 'index'])->name('biens');
        Route::post('/bien/create', [BiensController::class, 'store']);
        Route::get('/bien/listing', [BiensController::class, 'listing']);
        Route::post('/bien/modify/{id}', [BiensController::class, 'edit']);
        Route::post('/bien/remphoto', [BiensController::class, 'removePhoto']);
        Route::delete('/bien/delete/{id}', [BiensController::class, 'destroy']);

        // Local
        Route::post('/local/create', [LocalController::class, 'store']);
        Route::post('/local/listing', [LocalController::class, 'listing']);
        Route::post('/local/remphoto', [LocalController::class, 'removePhoto']);
        Route::post('/local/modify/{id}', [LocalController::class, 'edit']);
        Route::delete('/local/delete/{id}', [LocalController::class, 'destroy']);
    });

    // Mandat Gerance
    Route::group(['middleware' => ['permissionOrRoot:Menu.Mandat']], function () {
        Route::get('/gerance/index', [MandatGeranceController::class, 'index'])->name('gerance');
        Route::post('/gerance/create', [MandatGeranceController::class, 'store']);
        Route::get('/gerance/listing', [MandatGeranceController::class, 'listing']);
        Route::post('/gerance/remphoto', [MandatGeranceController::class, 'removePhoto']);
        Route::post('/gerance/modify/{id}', [MandatGeranceController::class, 'edit']);
        Route::delete('/gerance/delete/{id}', [MandatGeranceController::class, 'destroy']);
        Route::get('/gerance/getbien/{id_proprio}', [MandatGeranceController::class, 'getBienByProprio']);
        Route::post('/gerance/create_file_mandat', [MandatGeranceController::class, 'create']);
        Route::get('/gerance/getRepresentantProprio/{id_proprio}', [MandatGeranceController::class, 'getRepresentantByProprio']);
    });

    // Bail
    Route::group(['middleware' => ['permissionOrRoot:Menu.Bail']], function () {
        Route::get('/bail/index', [BailController::class, 'index'])->name('bail');
        Route::post('/bail/create', [BailController::class, 'store']);
        Route::get('/bail/getLocal/{id_bien}/{id_proprio}', [BailController::class, 'getLocalByBien']);
        Route::post('/bail/create_file_bail', [BailController::class, 'create']);
        Route::get('/bail/listing', [BailController::class, 'listing']);
        Route::get('/bail/getbien/{id_proprio}', [BailController::class, 'getBienByProprio']);
        Route::get('/bail/find_local_loue/{id_locataire}', [BailController::class, 'findLocalLoue']);
        Route::post('/bail/resiliation', [BailController::class, 'resiliationBail']);
    });

    // Operations
    Route::group(['middleware' => ['permissionOrRoot:Menu.Operations']], function () {
        Route::get('/operations/index', [OperationsController::class, 'index'])->name('operation');
        Route::get('/operations/paiement_loyer', [OperationsController::class, 'listingPaimentLoyer']);
        Route::post('/operations/ajout', [OperationsController::class, 'ajoutOperation']);
        Route::get('/operations/charges_frais', [OperationsController::class, 'charges'])->name('charges');
        Route::get('/operations/charges', [OperationsController::class, 'listingCharges']);
        Route::post('/operations/charge/create', [OperationsController::class, 'ajoutCharge']);
        Route::delete('/operations/charges/deletedecharge/{id}', [OperationsController::class, 'destroyCharge']);
        Route::get('/operations/list', [OperationsController::class, 'list'])->name('operationList');
        Route::get('/operations/listing', [OperationsController::class, 'listOperation']);
        Route::get('/operations/getbien/{id_proprio}', [OperationsController::class, 'getBienByProprio']);
        Route::get('/operations/loyers_actif/{id_locataire}', [OperationsController::class, 'getloyersActifByLocataire']);

        Route::post('/operations/ajoutPaiement', [OperationsController::class, 'ajoutPaiement']);

        Route::get('/operations/paiement-loyers/checkCanPayMoisLoyer/{id}/{mois}', [OperationsController::class, 'checkLoyerIsPayable']);
        Route::delete('/operations/paiements/delete/{id}', [OperationsController::class, 'destroyPaiement']);

        // Paiement Loyers New
        Route::get('/operations/paiement-loyers', [PaiementLoyerSysController::class, 'index'])->name('loyers');

        Route::get('/operations/paiement-loyers/listing', [PaiementLoyerSysController::class, 'listing']);
        Route::post('/operations/ajoutPaiementLoyerManuel', [OperationsController::class, 'ajoutPaiementLoyerManuel']);

        Route::get('/operations/versements', [VersementsController::class, 'index'])->name('versements');
        Route::get('/operations/versements/all', [VersementsController::class, 'all']);
        Route::post('/operations/versements/create', [VersementsController::class, 'ajoutVersement']);
        Route::post('/operations/versements/edit', [VersementsController::class, 'edit']);
        Route::delete('/operations/versements/deleteversement/{id}', [VersementsController::class, 'destroy']);


    });





    // Rapport
    Route::group(['middleware' => ['permissionOrRoot:Menu.Rapports']], function () {
        Route::get('/rapport/index', [RapportController::class, 'index'])->name('rapport');
        Route::get('/rapport/proprietaires', [ProprietairesRapport::class, 'index'])->name('proprietairesRapport');
        Route::get('/rapport/proprietaires/{id}', [ProprietairesRapport::class, 'show'])->name('proprietaires.show');
        Route::get('/rapports/loyers', [RapportController::class, 'rapportLoyers']);

        Route::get('rapports/loyers/locataires', [RapportController::class, 'rapportLocataires']);
        Route::get('rapports/loyers/proprietaires', [RapportController::class, 'rapportProprietaires']);
        Route::get('rapports/loyers/agence', [RapportController::class, 'rapportAgence']);

        Route::get('rapports/loyers/generation_encaissement_loyer', [RapportController::class, 'rapportPDFProprietaires']);
        Route::get('rapports/envoye_au_proprio', [RapportController::class, 'envoisRapportProprio']);
        Route::get('rapports/loyers/generation_rapport_locataire', [RapportController::class, 'rapportPDFLocataire']);
        Route::get('rapports/envoye_au_locataire', [RapportController::class, 'envoisRapportLocataire']);




    });

    // Personnel
    Route::get('/personnel/index', [PersonnelController::class, 'index'])->name('personnel');  

    // Preferences
    Route::group(['middleware' => ['permissionOrRoot:Menu.Preferences']], function () {
        Route::get('/preference/index', [PreferenceController::class, 'index'])->name('preference');
    });

    // Comptes
    Route::group(['middleware' => ['permissionOrRoot:Menu.MonCompte']], function () {
        Route::get('/compte/index', [CompteController::class, 'index'])->name('compte');
    });

    // Gestion Utilisateurs
    Route::group(['middleware' => ['permissionOrRoot:GestionUtilisateur.Menu']], function () {
        Route::get('/utilisateurs/index', [UserController::class, 'index'])->name('gutilisateurs');
    });

     // Gestion entite
    Route::group(['middleware' => ['permissionOrRoot:Menu.Agence']], function () {
        Route::get('/entite/index', [AgenceController::class, 'index'])->name('entites');
        Route::get('/entite/listing', [AgenceController::class, 'listing']);
        Route::get('/entite/create', [AgenceController::class, 'create']);

    });

    // Gestion Filiale
    Route::group(['middleware' => ['permissionOrRoot:Menu.Agence']], function () {
        Route::get('/filiale/index', [FilialeController::class, 'index'])->name('filiales');
        Route::post('/filiale/listing', [FilialeController::class, 'listing']);
        Route::post('/filiale/create', [FilialeController::class, 'store']);
        Route::post('/filiale/modify/{id}', [FilialeController::class, 'edit']);
        Route::delete('/filiale/delete/{id}', [FilialeController::class, 'destroy']);

    });

    // Global
    Route::get('/global/getsolde', [GlobalController::class, 'getSolde']);

    // Template
     Route::group(['as' => 'templates.', 'prefix' => 'templates'], function () {
        Route::get('/', [TemplateController::class, 'index'])->name('index');
        Route::post('/quittance-loyer/preview', [QuittanceLoyerTemplateController::class, 'preview'])->name('quittance-loyer.preview');

        Route::get('/getList', [TemplateController::class, 'getList'])->name('get-list');
        Route::get('/create', [TemplateController::class, 'create'])->name('create');
        Route::post('/', [TemplateController::class, 'store'])->name('store');
        Route::delete('/{template}', [TemplateController::class, 'delete'])->name('delete')->middleware(['ajax']);
        Route::get('/{template}/edit', [TemplateController::class, 'edit'])->name('edit');
        Route::patch('/{template}/edit', [TemplateController::class, 'update'])->name('update');
    });


    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::post('/{id}/mark-read', [NotificationController::class, 'markAsRead']);
        Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead']);
        Route::delete('/clear', [NotificationController::class, 'clearAll']);
        Route::get('/all', [NotificationController::class, 'all']);
        Route::get('/index', [NotificationController::class, 'allNotif'])->name('notif');

    });

     // Execution des Commandes manuellement
    Route::get('/commands/index', [TachesAutomatisesController::class, 'index'])->name('automate');
    Route::post('/commands/generate-loyers', [TachesAutomatisesController::class, 'runCommand']);
    Route::post('/commands/check-mandats', [TachesAutomatisesController::class, 'runCommand']);
    Route::post('/commands/send-mails', [TachesAutomatisesController::class, 'runCommand']);


});

// Middleware de protection par PIN Locataire
Route::group(['middleware' => ['pin.locataire.verified']], function () {
    Route::match(['get', 'post'], '/badge/locataire/{locataire_id}', [BadgeController::class, 'show'])->name('badgeLocataire.show');
});

Route::get('/badge/locataire/logout/{locataire_id}', function ($locataire_id) {
    session()->forget('pin_verified');
    session()->forget('pin_verified_at');

    return redirect()->route('badgeLocataire.show', ['locataire_id' => $locataire_id])
        ->with('message', 'Vous avez été déconnecté du badge.');
})->name('badgeLocataire.logout');

// Middleware de protection par PIN Proprietaire
Route::group(['middleware' => ['pin.proprietaire.verified']], function () {
    Route::match(['get', 'post'], '/badge/proprietaire/{proprietaire_id}', [BadgeController::class, 'showProprio'])->name('badgeProprietaire.show');
});

Route::get('/badge/proprietaire/logout/{proprietaire_id}', function ($proprietaire_id) {
    session()->forget('pin_verified');
    session()->forget('pin_verified_at');

    return redirect()->route('badgeProprietaire.show', ['proprietaire_id' => $proprietaire_id])
        ->with('message', 'Vous avez été déconnecté du badge.');
})->name('badgeProprietaire.logout');




Route::fallback(function () {
    abort(404);
});
