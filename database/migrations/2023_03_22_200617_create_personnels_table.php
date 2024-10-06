<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePersonnelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::create('personnels', function (Blueprint $table) {
            $table->id();
            $table->string('pers_id')->unique();
            $table->unsignedBigInteger('pers_user_id'); // Utilisation d'un entier non signé pour les clés étrangères
            $table->string('pers_nom');
            $table->string('pers_prenom');
            $table->string('pers_email');
            $table->string('pers_ind_1');
            $table->string('pers_tel_1');
            $table->string('pers_ind_2')->nullable();
            $table->string('pers_tel_2')->nullable();
            $table->string('pers_adress')->nullable();
            $table->string('pers_ville')->nullable();
            $table->string('pers_pays')->nullable();
            $table->string('user');
            $table->timestamps();

            // Ajout de la clé étrangère
            $table->foreign('pers_user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('personnels');
    }
}
