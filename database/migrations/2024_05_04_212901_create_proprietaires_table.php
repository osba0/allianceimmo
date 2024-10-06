<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProprietairesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('proprietaires', function (Blueprint $table) {
             $table->id();
            $table->string('proprio_id')->unique();
            $table->string('proprio_nom');
            $table->string('proprio_prenom');
            $table->string('proprio_profession');
            $table->string('proprio_nationalite');
            $table->string('proprio_ville_naissance');
            $table->string('proprio_date_naissance')->nullable();
            $table->string('proprio_pays_naissance')->nullable();
            $table->string('proprio_entreprise')->nullable();
            $table->string('proprio_indicatif_1')->nullable();
            $table->string('proprio_tel_1')->nullable();
            $table->string('proprio_indicatif_2')->nullable();
            $table->string('proprio_tel_2')->nullable();
            $table->string('proprio_adresse')->nullable();
            $table->string('proprio_ville')->nullable();
            $table->string('proprio_cp')->nullable();
            $table->string('proprio_pays')->nullable();
            $table->string('user');
            $table->string('proprio_compte_bancaire')->nullable();
            $table->string('proprio_email');
            $table->string('proprio_type_piece')->nullable();
            $table->string('proprio_numero_piece')->nullable();
            $table->text('proprio_kyc')->nullable();
            $table->string('proprio_date_expiration')->nullable();
            $table->boolean('proprio_etat')->default(true);
            $table->unsignedBigInteger('agence_id');
            $table->unsignedBigInteger('filiale_id')->nullable();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('agence_id')->references('id')->on('agences');
            $table->foreign('filiale_id')->references('id')->on('filiales');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('proprietaires');

    }
}
