<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRepresentantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('representants', function (Blueprint $table) {
            $table->id();
            $table->string('repr_id')->unique(); 
            $table->string('repr_id_proprio'); 
            $table->string('repr_civilite');
            $table->string('repr_nom');
            $table->string('repr_prenom');
            $table->string('repr_email')->nullable();
            $table->string('repr_tel_1')->nullable();
            $table->string('repr_indicatif_1')->nullable();
            $table->string('repr_type_piece')->nullable();
            $table->string('repr_numero_piece')->nullable();
            $table->string('repr_user');
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
        Schema::dropIfExists('representants');
    }
}
