<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaiementsLoyersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('paiements_loyers', function (Blueprint $table) {
            $table->id();
            $table->string('paiement_id')->unique(); 
            $table->string('paiement_bail_id'); 
            $table->double('paiement_montant');
            $table->date('paiement_mois_location'); 
            $table->string('paiement_echeance');
            $table->text('paiement_recu');
            $table->integer('paiement_etat'); // 0-impayé; 1-payé; 2-paiement_partiel....
            $table->boolean('paiement_cloture');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('paiements_loyers');
    }
}
