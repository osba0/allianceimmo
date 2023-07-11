<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAgencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('agences', function (Blueprint $table) {
            $table->id();
            $table->string('agence_id')->unique(); 
            $table->string('agence_nom'); 
            $table->string('agence_slogan')->nullable(); 
            $table->string('agence_activite')->nullable(); 
            $table->string('agence_ninea')->nullable();
            $table->string('agence_email')->nullable();
            $table->string('agence_ind1');
            $table->string('agence_tel1'); 
            $table->string('agence_ind2')->nullable(); 
            $table->string('agence_tel2')->nullable(); 
            $table->string('agence_adresse');
            $table->string('agence_ville');
            $table->string('agence_pays');
            $table->string('agence_logo')->nullable(); 
            $table->string('agence_user'); 
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
        Schema::dropIfExists('agences');
    }
}
