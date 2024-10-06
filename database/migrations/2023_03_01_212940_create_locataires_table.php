<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLocatairesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('locataires', function (Blueprint $table) {
            $table->id();
            $table->string('locat_id')->unique(); 
            $table->string('locat_type'); // Entreprise / Particulier / Autre
            $table->string('locat_civilite'); // Homme / Femme
            $table->string('locat_nom');
            $table->string('locat_prenom');
            $table->string('locat_date_naissance');
            $table->string('locat_pays_naissance');
            $table->string('locat_societe')->nullable();
            $table->string('locat_num_tva')->nullable();
            $table->string('locat_ninea_rc')->nullable();
            $table->string('locat_domaine_activite')->nullable();
            $table->string('locat_photo_perso')->nullable();
            $table->string('locat_profession');
            $table->string('locat_revenu_mensuel')->nullable();
            $table->string('locat_justificatif_revenu')->nullable();
            $table->string('locat_email')->unique();
            $table->string('locat_indicatif_1');
            $table->string('locat_tel_1');
            $table->string('locat_indicatif_2')->nullable();
            $table->string('locat_tel_2')->nullable();
            $table->string('locat_adresse')->nullable();
            $table->string('locat_ville')->nullable();
            $table->string('locat_code_postal')->nullable();
            $table->string('locat_region')->nullable();
            $table->string('locat_pays')->nullable();
            $table->string('locat_numero_piece');
            $table->string('locat_photo_piece');
            $table->string('locat_justicatif_revenu')->nullable();
            $table->string('locat_type_piece')->nullable();
            $table->string('locat_date_expiration')->nullable(); 
            $table->double('locat_avoir')->default(0); 
            $table->boolean('locat_etat')->default(1);
            $table->string('locat_user');
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
        Schema::dropIfExists('locataires');
    }
}
