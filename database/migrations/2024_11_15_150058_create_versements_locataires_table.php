<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVersementsLocatairesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('versements_locataires', function (Blueprint $table) {
            $table->id();
            $table->string('locataire_id');
            $table->decimal('montant', 15, 2);
            $table->decimal('solde_utilise', 15, 2)->default(0); // Montant déjà utilisé pour le paiement des loyers
            $table->decimal('solde_disponible', 15, 2); // Montant restant disponible après utilisation
            $table->string('mode_paiement')->nullable(); // Exemple : virement, espèce, mobile money
            $table->text('reference_paiement')->nullable(); // référence bancaire ou transactionnelle.
            $table->date('date_versement');
            $table->enum('statut', ['actif', 'epuise'])->default('actif');
            $table->timestamps();

            // Clé étrangère vers la table locataires
            $table->foreign('locataire_id')->references('locat_id')->on('locataires')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('versements_locataires');
    }
}
