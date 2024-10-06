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
use App\Http\Controllers\Rapports\ProprietairesRapport;


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
    });

    // Operations
    Route::group(['middleware' => ['permissionOrRoot:Menu.Operations']], function () {
        Route::get('/operations/index', [OperationsController::class, 'index'])->name('operation');
        Route::get('/operations/paiement_loyer', [OperationsController::class, 'listingPaimentLoyer']);
        Route::post('/operations/ajout', [OperationsController::class, 'ajoutOperation']);
        Route::get('/operations/charges_frais', [OperationsController::class, 'charges'])->name('charges');
        Route::get('/operations/charges', [OperationsController::class, 'listingCharges']);
        Route::post('/operations/charge/create', [OperationsController::class, 'ajoutCharge']);
        Route::get('/operations/list', [OperationsController::class, 'list'])->name('operationList');
        Route::get('/operations/listing', [OperationsController::class, 'listOperation']);
        Route::get('/operations/getbien/{id_proprio}', [OperationsController::class, 'getBienByProprio']);
    });


    // Rapport
    Route::group(['middleware' => ['permissionOrRoot:Menu.Rapports']], function () {
        Route::get('/rapport/index', [RapportController::class, 'index'])->name('rapport');
        Route::get('/rapport/proprietaires', [ProprietairesRapport::class, 'index'])->name('proprietairesRapport');
        Route::get('/rapport/proprietaires/{id}', [ProprietairesRapport::class, 'show'])->name('proprietaires.show');
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
});

Route::fallback(function () {
    abort(404);
});
