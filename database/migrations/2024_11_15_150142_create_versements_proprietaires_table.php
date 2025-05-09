<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVersementsProprietairesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('versements_proprietaires', function (Blueprint $table) {
            $table->id();
            $table->string('versement_id')->unique();
            $table->string('versement_proprio_id'); // Clé étrangère de type string
            $table->string('versement_bien_id')->nullable();
            $table->decimal('versement_montant', 15, 2);
            $table->enum('versement_type', ['achat', 'entretien', 'investissement', 'autre'])->default('autre');
            $table->text('versement_description')->nullable();
            $table->date('versement_date');
            $table->string('versement_moyen_paiement', 100)->default('Espèces');
            $table->string('versement_user');
            $table->text('versement_fichier')->nullable();
            $table->string('versement_reserve_1')->nullable();
            $table->string('versement_reserve_2')->nullable();
            $table->string('versement_reserve_3')->nullable();
            $table->timestamps();

            $table->foreign('versement_proprio_id')->references('proprio_id')->on('proprietaires')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         Schema::dropIfExists('versements_proprietaires');
    }
}
